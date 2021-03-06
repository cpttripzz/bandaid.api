<?php

use Symfony\Component\HttpFoundation\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

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

class LoadRegions extends AbstractFixture
    implements
    OrderedFixtureInterface,
    FixtureInterface,
    ContainerAwareInterface
{

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


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . 'region.yml';
        $yml = Yaml::parse(file_get_contents($filename));
        foreach ($yml['region'] as $row => $data) {
            $region = new Region();
            $country = $manager->getRepository('ZE\BABundle\Entity\Country')->findOneById($data['country_id']);

            $region->setCountry($country);
            $region->setLongName($data['long_name']);
            $region->setShortName($data['short_name']);
            $manager->persist($region);
        }
        $manager->flush();


    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }
}
