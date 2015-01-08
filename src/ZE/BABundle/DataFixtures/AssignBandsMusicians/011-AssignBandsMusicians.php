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
use ZE\BABundle\Entity\BandMusician;
use ZE\BABundle\Entity\City;
use ZE\BABundle\Entity\Country;
use ZE\BABundle\Entity\Document;
use ZE\BABundle\Entity\Genre;
use ZE\BABundle\Entity\Instrument;
use ZE\BABundle\Entity\Item;
use ZE\BABundle\Entity\Musician;
use ZE\BABundle\Entity\Region;

class AssignBandsMusicians extends AbstractFixture
    implements OrderedFixtureInterface,
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $manager;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;


        try {
            $bands = $this->manager->getRepository('ZE\BABundle\Entity\Band')->findAll();
            $musicians = $this->manager->getRepository('ZE\BABundle\Entity\Musician')->findAll();
            foreach ($bands as $band) {
                $musArr = array();

                for ($i = 0; $i < rand(1, 5); $i++) {
                    $randomMusician = $musicians[rand(0, count($musicians) - 1)];
                    if (!in_array($randomMusician->getId(), $musArr)) {
                        $musArr[] = $randomMusician->getId();
                        $this->manager->flush();
                        $this->container->get('ze.band_manager_service')->addMusicianToBand($randomMusician, $band);
                    }

                }
            }
            $this->manager->flush();


        } catch (\Exception $e) {
            echo($e->getMessage());
            $this->manager = $this->container->get('doctrine')->resetManager();
        }
    }

    /**
     * @param $assoc
     */
    public function createRandomImage($imagePath = 'people')
    {
        $document = new Document();
        $file = file_get_contents('http://lorempixel.com/300/150/' . $imagePath);
        $pwd = getcwd();
        $filename = sha1(uniqid(mt_rand(), true));
        $document->setName($filename);
        $document->setPath($filename . '.jpeg');
        $filename = $pwd . '/web/uploads/documents/' . $filename . '.jpeg';
        file_put_contents($filename, $file);
        $this->manager->persist($document);
        return $document;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }


}
