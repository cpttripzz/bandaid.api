<?php

namespace ZE\BABundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ZE\BABundle\Entity;

class Musician extends EntityRepository
{

    public function getAllMusiciansOwnedByUserId($userId, $returnQb=false)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.user = :userId')
            ->andWhere('a  INSTANCE OF ZE\BABundle\Entity\Musician')
            ->setParameter('userId', $userId);
        if($returnQb){
            return $qb;
        }
        return $qb->getQuery()->getResult();
    }

    public function findAllMusiciansByBandId($bandId, $returnQb=false)
    {
        /*SELECT
        FROM
          band_musician
          INNER JOIN association
            ON band_musician.musician_id = association.id
        WHERE `type` = 'musician'
            AND band_id = 4     */
        $qb = $this->createQueryBuilder('m');
        $qb ->innerJoin('m.bands','bands')
            ->where('bands.id = :bandId')
            ->setParameter('bandId', $bandId);
        if($returnQb){
            return $qb;
        }
        return $qb->getQuery()->getResult();
    }
}