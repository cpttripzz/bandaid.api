<?php

namespace ZE\BABundle\Service\Util;


use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use ZE\BABundle\Entity\City;
use ZE\BABundle\Entity\Address;
use ZE\BABundle\Entity\Region;
use Google\GeolocationBundle\Geolocation\GeolocationApi;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LocationManager
{
    private $security;
    private $em;
    private $geolocationApi;

    public function __construct(SecurityContext $security, EntityManager $em, GeolocationApi $geolocationApi)
    {
        $this->security = $security;
        $this->em = $em;
        $this->geolocationApi = $geolocationApi;
    }


    public function getOrCreateRegion($city)
    {

        $cityName = $city->getName() . ', ' . $city->getCountry()->getName();
        $geo = $this->geolocationApi;
        $location = $geo->locateAddress($cityName);
        $result = json_decode($location->getResult(), true);

        foreach ($result[0]['address_components'] as $addressComponent) {
            if (!isset($addressComponent['types'][0])) {
                return null;
            }
            $type = $addressComponent['types'][0];
            if ($type == 'administrative_area_level_1') {
                $regionShortName = $addressComponent['short_name'];
                $regionLongName = $addressComponent['long_name'];

                $region = $this->em->getRepository('ZE\BABundle\Entity\Region')->findOneByLongName($regionLongName);
                if (!$region) {
                    $region = new Region();
                    $region->setCountry($city->getCountry());
                    $region->setShortName($regionShortName);
                    $region->setLongName($regionLongName);
                    $this->em->persist($region);
                    $this->em->flush();
                }
                return $region;
            }

        }
    }

    public function saveNewAddress($associationId, $associationType, $address)
    {
        if ($associationType == 'band') {
            $association = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($associationId);
        } elseif ($associationType == 'musician') {
            $association = $this->em->getRepository('ZE\BABundle\Entity\Musician')->findOneById($associationId);
        }
        if (false === $this->security->isGranted('edit', $association)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $arrAddress = explode(',', $address);
        $strCountry = trim($arrAddress[count($arrAddress) - 1]);
        $strCity = trim($arrAddress[1]);
        $strAddress = trim($arrAddress[0]);

        $country = $this->em->getRepository('ZE\BABundle\Entity\Country')->findOneByName($strCountry);

        $city = $this->getOrCreateCity($strCity, $country);
        $region = $this->getOrCreateRegion($city);
        $address = $this->getOrCreateAddress($strAddress,$city,$region);
        $association->addAddress($address);
        $this->em->persist($association);
        try {
            $this->em->flush();
        }
        catch (\Exception $e){
            return false;
        }
        return array('id'=>$address->getId(), 'text'=>$address->__toString());

    }

    public function getAllAddressesForAssociation($associationId, $associationType)
    {
        if ($associationType == 'band') {
            $association = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($associationId);
        } elseif ($associationType == 'musician') {
            $association = $this->em->getRepository('ZE\BABundle\Entity\Musician')->findOneById($associationId);
        }
        if (false === $this->security->isGranted('edit', $association)) {
            throw new AccessDeniedException('Unauthorised access!');
        }


        return $association->getAddresses();

    }

    public function getOrCreateCity($cityName, $country)
    {

        $city = $this->em->getRepository('ZE\BABundle\Entity\City')->findOneByName($cityName);
        if (!$city) {
            $city = new City();
            $city->setName($cityName);
            $city->setCountry($country);
            $this->em->persist($city);
            $this->em->flush();
        }
        return $city;
    }

    public function getOrCreateAddress($strAddress,$city, $region)
    {
        $address = $this->em->getRepository('ZE\BABundle\Entity\Address')->findOneByAddressAndCityAndRegion($strAddress,$city,$region);
//        $region = $this->em->getRepository('ZE\BABundle\Entity\Region')->findOneByAddressAndCityAndRegion($address,$city->getId(),$regionId);
        if (!$address) {
            $address = new Address();
            $address->setAddress($strAddress);
            $city->setRegion($region);
            $address->setCity($city);

            $this->em->persist($address);
            $this->em->flush();
        }
        return $address;
    }
}