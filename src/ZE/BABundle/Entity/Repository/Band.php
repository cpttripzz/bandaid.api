<?php

namespace ZE\BABundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ZE\BABundle\Entity;

class Band extends EntityRepository
{
    public function getAllBandsOwnedByUserId($userId)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.user = :userId')
            ->andWhere('a  INSTANCE OF ZE\BABundle\Entity\Band')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }


    public function findAllBandsByMusicianId($musicianId, $returnQb=false)
    {
        $qb = $this->createQueryBuilder('b');
        $qb ->innerJoin('b.musicians','musicians')
            ->where('musicians.id = :musicianId')
            ->setParameter('musicianId', $musicianId);
        if($returnQb){
            return $qb;
        }
        return $qb->getQuery()->getResult();
    }


}