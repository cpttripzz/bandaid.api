<?php

namespace ZE\BABundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ZE\BABundle\Entity\Band;
use ZE\BABundle\Exception\InvalidFormException;
use ZE\BABundle\Form\BandType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use ZE\BABundle\Doctrine\Hydrator\ArrayHydrator;

class BandHandler
{
    private $em;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $authorizationCheckerInterface;
    private $tokenStorage;

    public function __construct(EntityManager $em, $entityClass, FormFactoryInterface $formFactory,
        AuthorizationCheckerInterface $authorizationCheckerInterface, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->authorizationCheckerInterface = $authorizationCheckerInterface;
        $this->tokenStorage = $tokenStorage;
    }


    public function get($id)
    {
        return $this->repository->find($id);
    }


    public function save($request)
    {
        if(isset($request['id'])) {
            $entity = $this->get($request['id']);

            if (!$entity) {
                throw new NotFoundHttpException('Unable to find Band entity.');
            }
            if (false === $this->authorizationCheckerInterface->isGranted('edit', $entity)) {
                throw new AccessDeniedException('Unauthorised access!');
            }
        } else {
            $entity = new Band();
            $user = $this->tokenStorage->getToken()->getUser();
            $entity->setUser($user);
        }
        $arrPropsToUnset = array('userId','type','useritems','createdAt','updatedAt','id');
        $arrPropsToNullify = array('slug');

        if(isset($request['band'])) {
            $request = $request['band'];
        }
        foreach($arrPropsToNullify as $propToNullify){
            $request[$propToNullify] = null;
        }
        foreach ($arrPropsToUnset as $propsToUnset) {
            if (array_key_exists($propsToUnset,$request)){
                unset($request[$propsToUnset]);
            }
        }
        $arrayHydrator = new ArrayHydrator($this->em);
        $entity = $arrayHydrator->hydrateFromArray($entity, $request);

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }


    private function createBand()
    {
        return new $this->entityClass();
    }

}