<?php

namespace ZE\BABundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ZE\BABundle\Entity;

class BandMusician extends EntityRepository
{

    public function findAllMusiciansByBandId($bandId)
    {
        /*SELECT
        FROM
          band_musician
          INNER JOIN association
            ON band_musician.musician_id = association.id
        WHERE `type` = 'musician'
            AND band_id = 4     */

        $qb = $this->createQueryBuilder('bm')
            ->innerJoin('bm.musician', 'm')
            ->where('bm.band = :bandId')
            ->setParameter('bandId', $bandId);
        return $qb->getQuery()->getResult();
    }

}