<?php

namespace ZE\BABundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ZE\BABundle\Entity;

class Document extends EntityRepository
{
    public function getAllImagesByAssociationId($associationId)
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.association', 'a')
            ->where('a.id = :associationId')
            ->andWhere('d.type = :documentType')
            ->setParameter('associationId', $associationId)
            ->setParameter('documentType', Entity\Document::DOCUMENT_TYPE_IMAGE);
        $arrResult = array();
        foreach($qb->getQuery()->getResult() as $document){
            $arrResult[] = array(
                'id' => $document->getId(),
                'name' => $document->getName(),
                'webpath' => $document->getWebPath(),
                'association_id' => $document->getAssociation()->getId()
            );
        }
        return $arrResult;
    }

}