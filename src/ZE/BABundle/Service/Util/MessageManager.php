<?php

namespace ZE\BABundle\Service\Util;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use ZE\BABundle\Entity\Band;
use ZE\BABundle\Entity\BandMusician;
use ZE\BABundle\Entity\Address;
use ZE\BABundle\Entity\Musician;

class MessageManager
{
    private $security;
    private $em;
    public function __construct(SecurityContext $security, EntityManager $em)
    {
        $this->security = $security;
        $this->em = $em;
    }


}