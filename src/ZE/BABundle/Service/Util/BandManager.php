<?php

namespace ZE\BABundle\Service\Util;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use ZE\BABundle\Entity\Band;
use ZE\BABundle\Entity\BandMusician;
use ZE\BABundle\Entity\Address;
use ZE\BABundle\Entity\Musician;

class BandManager
{
    private $security;
    private $em;
    public function __construct(SecurityContext $security, EntityManager $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function isUserInBand(Band $band)
    {
        if( !$this->security->isGranted('IS_AUTHENTICATED_FULLY') ){
            return false;
        }
        $bandMembers = $band->getMusicians();
        foreach ($bandMembers as $bandMember){
            if($bandMember->getUser()->getId() ==  $this->security->getToken()->getUser()->getId()){
                return true;
            }
        }
        return false;

    }
    public function findAllAssociationsByProximityToAddress($associationType,Address $address)
    {
        $addresses = $this->em->getRepository('ZEBABundle:Address')->getClosestAddresses($address);
        $addressIds = array();
        foreach($addresses as $address){
            $addressIds[] = $address->getId();
        }
        $associations = $this->em->getRepository('ZEBABundle:Association')
            ->getAllAssociationsByTypeAndAddressIds($associationType,$addressIds);
        return $associations;
    }

    public function isMusicianInBand(Musician $musician, Band $band)
    {
        $musicianId = $musician->getId();
        $bandMembers = $band->getMusicians();
        foreach ($bandMembers as $bandMember){
            if($bandMember->getId() ==  $musicianId ){
                return true;
            }
        }
        return false;
    }
    public function addMusicianToBand(Musician $musician, Band $band)
    {
        if($this->isMusicianInBand($musician,$band)){
            return false;
        }
        $band->addMusician($musician);
        $musician->addBand($band);
        $this->em->persist($band);
        $this->em->persist($musician);
        $this->em->flush();
    }
}