<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 27/12/14
 * Time: 00:40
 */

namespace ZE\BABundle\Doctrine\Hydrator;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use ZE\BABundle\Util\StringUtil;
use Doctrine\Common\Inflector\Inflector;

class ArrayHydrator {
    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public  function hydrateFromArray($entity,$array)
    {
        foreach ($array as $property=>$value)
        {
            if (is_array($value)){
                try {
                    $namespace = StringUtil::before_last('\\', get_class($entity));
                    $newPropArrayCollection = new ArrayCollection();
                    $repository = $this->em->getRepository($namespace . '\\' . Inflector::singularize($property));
                    $entity->{'removeAll' . ucwords($property)}($newPropArrayCollection);
                    $this->em->persist($entity);
                    $this->em->flush($entity);
                    foreach ($value as $propId) {
                        $propertyEntity = $repository->find($propId);
                        $newPropArrayCollection->add($propertyEntity);
                    }
                    $entity->{'set' . ucwords($property)}($newPropArrayCollection);
                } catch (\Exception $e){
                    continue;
                }
            } else {
                $prop = 'set' .ucwords($property);
                if(property_exists($entity, $prop )) {
                    $entity->{$prop}($value);
                }
            }
            $this->em->persist($entity);
            $this->em->flush($entity);

        }
        return $entity;
    }
} 