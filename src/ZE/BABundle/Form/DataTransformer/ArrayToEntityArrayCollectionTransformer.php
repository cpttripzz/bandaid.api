<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 26/12/14
 * Time: 20:24
 */

namespace ZE\BABundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToEntityArrayCollectionTransformer implements DataTransformerInterface {

    private $em;
    private $classType;
    /**
     * @param ObjectManager $om
     */
    public function __construct($em, $classType)
    {
        $this->em = $em;
        $this->classType = $classType;
    }

    public function transform( $arrayCollection)
    {
        $arrReturn = array();
        if (null === $arrayCollection) {
            return $arrReturn;
        }
        foreach ($arrayCollection as $arrayCollectionItem){
            $arrReturn[] = $arrayCollectionItem->getId();
        }
        return $arrReturn;
    }

    public function reverseTransform($arrayOfIds)
    {
        if (!$arrayOfIds) {
            return null;
        }

        $arrayCollection = new ArrayCollection();
        foreach($arrayOfIds as $id){
            $repository = $this->em->getRepository($this->classType);
            $entity = $repository->find($id);
            $arrayCollection->add($entity);
        }

        return $arrayCollection;
    }
}