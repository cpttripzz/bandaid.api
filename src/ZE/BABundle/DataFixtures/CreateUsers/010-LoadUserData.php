<?php
// src/Application/Sonata/UserBundle/DataFixtures/ORM/010-LoadUserData.php
namespace Application\Sonata\UserBundle\DataFixtures\ORM;

use Symfony\Component\HttpFoundation\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use FOS\UserBundle\Entity\Group;
use Application\Sonata\UserBundle\Entity\User;
use Faker;
use ZE\BABundle\Entity\Address;
use ZE\BABundle\Entity\Band;

use ZE\BABundle\Entity\City;
use ZE\BABundle\Entity\Country;
use ZE\BABundle\Entity\Document;
use ZE\BABundle\Entity\Genre;
use ZE\BABundle\Entity\Instrument;
use ZE\BABundle\Entity\Item;
use ZE\BABundle\Entity\Musician;
use ZE\BABundle\Entity\Region;

class LoadUserData extends AbstractFixture
    implements OrderedFixtureInterface,
    FixtureInterface,
    ContainerAwareInterface
{

    private $run = true;
    private $container;

    private $cities;
    private $genres;
    private $faker;
    private $manager;
    private $instruments;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getRandomGenre()
    {
        return $this->genres[rand(0, count($this->genres) - 1)];
    }

    public function getRandomInstrument()
    {
        return $this->instruments[rand(0, count($this->instruments) - 1)];
    }

    public function loadInstruments()
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . '003-instruments.yml';
        $yml = Yaml::parse(file_get_contents($filename));
        foreach ($yml['instrument'] as $row => $data) {
            $instrument = new Instrument();
            $instrument->setName($data['name']);
            $this->manager->persist($instrument);
        }
        $this->manager->flush();

    }

    public function loadCities()
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . '002-cities.yml';
        $yml = Yaml::parse(file_get_contents($filename));
        foreach ($yml['city'] as $row => $data) {
            $city = new City();
            $city->setName($data['name']);
            $city->setLatitude($data['latitude']);
            $city->setLongitude($data['longitude']);
            $country = $this->manager->getRepository('ZE\BABundle\Entity\Country')->findOneByCode($data['country_id']);
            $city->setCountry($country);
            $this->manager->persist($city);
        }
        $this->manager->flush();

    }

    public function createRandomAddress()
    {
        $city = $this->cities[rand(0, count($this->cities) - 1)];
        $client = $this->container->get('jcroll_foursquare_client');

        $command = $client->getCommand('venues/explore', array(
            'll' => $city->getLatitude() . ',' . $city->getLongitude()
        ));
        $results = $command->execute();

        if (!empty($results['meta']['code']) && $results['meta']['code'] == 200) {
            if (empty($results['response']['groups'][0]['items'])) {
                return false;
            }
            foreach ($results['response']['groups'][0]['items'] as $item) {
                if (empty($item['venue']['location']['address'])) {
                    continue;
                }

                $stamItem = $this->manager->getRepository('ZE\BABundle\Entity\Item')->findOneByFsId($item['venue']['id']);
                if ($stamItem) {
                    continue;
                }
                $stamItem = new Item();
                $stamItem->setFsId($item['venue']['id']);

                $address = new Address();
                $address->setCity($city);
                $address->setAddress($item['venue']['location']['address']);
                if (!empty($item['venue']['location']['lat']) && !empty($item['venue']['location']['lng'])) {
                    $address->setLatitude($item['venue']['location']['lat']);
                    $address->setLongitude($item['venue']['location']['lng']);
                }
                $region = $this->getOrCreateRegion($city);
                if ($region) {
                    $address->setRegion($region);
                }
                $this->manager->persist($stamItem);
                $this->manager->persist($address);
                return $address;
            }
        }
    }

    public function getOrCreateRegion($city)
    {

        $cityName = $city->getName() . ', ' . $city->getCountry()->getName();
        $geo = $this->container->get('google_geolocation.geolocation_api');
        $location = $geo->locateAddress($cityName);
        $result = json_decode($location->getResult(), true);

        foreach ($result[0]['address_components'] as $addressComponent) {
            if (!isset($addressComponent['types'][0])) {
                return false;
            }
            $type = $addressComponent['types'][0];
            if ($type == 'administrative_area_level_1') {
                $regionShortName = $addressComponent['short_name'];
                $regionLongName = $addressComponent['long_name'];

                $region = $this->manager->getRepository('ZE\BABundle\Entity\Region')->findOneByLongName($regionLongName);
                if (!$region) {
                    $region = new Region();
                    $region->setCountry($city->getCountry());
                    $region->setShortName($regionShortName);
                    $region->setLongName($regionLongName);
                    $this->manager->persist($region);
                }
                return $region;
            }

        }
    }


    public function createRandomBand()
    {
        $assoc = new Band();
        $assoc->setName(str_replace('.', '', $this->faker->sentence($nbWords = rand(1, 5))));
        $assoc->setDescription($this->faker->text($maxNbChars = 200));
        for ($i = 0; $i < rand(1, 3); $i++) {
            $genre = $this->getRandomGenre();
            $gs = $assoc->getGenres();
            $arrGenres = array();
            if (!empty($gs)) {
                foreach ($gs as $g) {
                    $arrGenres[] = $g->getName();
                }
            }
            if (!in_array($genre->getName(), $arrGenres)) {
                $assoc->addGenre($genre);
            } else {
                continue;
            }
        }
        for ($i = 0; $i < rand(1, 2); $i++) {
            $address = $this->createRandomAddress();
            $address->addAssociation($assoc);
            $assoc->addAddress($address);
        }

        $document = $this->createRandomImage('nightlife');
        $document->setAssociation($assoc);
        $this->manager->persist($assoc);
        return $assoc;
    }

    public function createRandomMusician()
    {
        $assoc = new Musician();
        $assoc->setName(str_replace('.', '', $this->faker->sentence($nbWords = rand(1, 5))));
        $assoc->setDescription($this->faker->text($maxNbChars = 200));
        $arrGenres = array();
        $arrInstruments = array();
        for ($i = 0; $i < rand(1, 3); $i++) {
            $genre = $this->getRandomGenre();
            $gs = $assoc->getGenres();

            if (!in_array($genre->getName(), $arrGenres)) {
                $assoc->addGenre($genre);
                $arrGenres[] = $genre->getName();
            } else {
                continue;
            }

        }
        for ($i = 0; $i < rand(1, 2); $i++) {
            $address = $this->createRandomAddress();
            $address->addAssociation($assoc);
            $assoc->addAddress($address);
        }
        for ($i = 0; $i < rand(1, 2); $i++) {
            $instrument = $this->getRandomInstrument();
            if (!in_array($instrument->getName(), $arrInstruments)) {
                $assoc->addInstrument($instrument);
                $arrInstruments[] = $instrument->getName();
            }
        }
        $document = $this->createRandomImage();
        $document->setAssociation($assoc);
        $this->manager->persist($assoc);
        return $assoc;
    }

    public function loadGenres()
    {
        $genres = array('Avant Garde', 'Blues', 'Country', 'Folk', 'Funk', 'Heavy Metal', 'Jazz', 'Punk Rock', 'Rap', 'Rock', 'Reggae');
        foreach ($genres as $genre) {
            $g = new Genre();
            $g->setName($genre);
            $this->manager->persist($g);
        }
        $this->manager->flush();

    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->cities = $this->manager->getRepository('ZE\BABundle\Entity\City')->findAll();
        if (empty($this->cities)) {
            $this->loadCities();
            $this->cities = $this->manager->getRepository('ZE\BABundle\Entity\City')->findAll();
        }

        $this->instruments = $this->manager->getRepository('ZE\BABundle\Entity\Instrument')->findAll();
        if (empty($this->instruments)) {
            $this->loadInstruments();
            $this->instruments = $this->manager->getRepository('ZE\BABundle\Entity\Instrument')->findAll();
        }

        $this->genres = $this->manager->getRepository('ZE\BABundle\Entity\Genre')->findAll();
        if (empty($this->genres)) {
            $this->loadGenres();
            $this->genres = $this->manager->getRepository('ZE\BABundle\Entity\Genre')->findAll();
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $groupManager = $this->container->get('fos_user.group_manager');
        $userGroup = $groupManager->findGroupByName('user');
        if (!$userGroup) {
            $userGroup = $groupManager->createGroup('user');
            $groupManager->updateGroup($userGroup, true);
        }
        $this->faker = Faker\Factory::create();

        for ($x = 0; $x < 5; $x++) {
            try {
                $user = $userManager->createUser();
                $user->setUsername($this->faker->userName);
                $user->setEmail($this->faker->email);
                $user->setPlainPassword('123456');

                $user->setDateOfBirth($this->faker->dateTimeBetween($startDate = '-80 years', $endDate = '-20 years'));
                $user->setFirstname($this->faker->firstName);
                $user->setLastname($this->faker->lastName);
                $gender = (rand(0, 1) == 1 ? 'm' : 'f');
                $user->setGender($gender);
                $user->setPhone('111-111-1111');
                $user->addGroup($userGroup);
                $user->setEnabled(true);
                $user ->setRoles(array('ROLE_USER'));

                $randomAssociation = rand(1, 100);
                if ($randomAssociation > 90 || $randomAssociation > 50) {
                    for ($j = 0; $j < rand(1, 2); $j++) {
                        $band = $this->createRandomBand();
                        $user->addBand($band);
                        $band->setUser($user);
                        $musician = $this->createRandomMusician();
                        $user->addMusician($musician);
                        $musician->setUser($user);
                        $this->manager->flush();
                        $this->container->get('ze.band_manager_service')->addMusicianToBand($musician, $band);
                    }
                }

                if ($randomAssociation > 90 || $randomAssociation < 50) {
                    for ($j = 0; $j < rand(1, 2); $j++) {
                        $musician = $this->createRandomMusician();
                        $user->addMusician($musician);
                        $musician->setUser($user);
                    }
                }
                $userManager->updateUser($user, true);
                $this->manager->flush();
                echo("\n user created:" . $user->getUsername());
            } catch (\Exception $e) {
                echo($e->getMessage());
                $this->manager = $this->container->get('doctrine')->resetManager();
                continue;
            }
        }
    }


    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

    /**
     * @param $assoc
     */
    public function createRandomImage($imagePath = 'people')
    {
        $document = new Document();
        $file = file_get_contents('http://lorempixel.com/300/225/' . $imagePath);
        $pwd = getcwd();
        $filename = sha1(uniqid(mt_rand(), true));
        $document->setPath($filename . '.jpeg');
        if (!is_dir($pwd . '/web/img/users/')) {
            mkdir($pwd . '/web/img/users/');
        }
        $filename = $pwd . '/web/img/users/' . $filename . '.jpeg';
        file_put_contents($filename, $file);
        $this->manager->persist($document);


        return $document;
    }

}
