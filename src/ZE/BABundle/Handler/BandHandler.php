<?php

namespace ZE\BABundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    public function __construct(EntityManager $em, $entityClass, FormFactoryInterface $formFactory, AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->authorizationCheckerInterface = $authorizationCheckerInterface;
    }


    public function get($id)
    {
        return $this->repository->find($id);
    }


    public function post(Request $request, $id)
    {
        $entity = $this->get($id);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find Band entity.');
        }
        if (false === $this->authorizationCheckerInterface->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $arrPropsToUnset = array('userId','type','useritems','createdAt','updatedAt');
        $arrPropsToNullify = array('slug');

        $parameters = $request->request->all();
        $parameters = $parameters['band'];
        foreach($arrPropsToNullify as $propToNullify){
            $parameters[$propToNullify] = null;
        }
        foreach ($arrPropsToUnset as $propsToUnset) {
            if (array_key_exists($propsToUnset,$parameters)){
                unset($parameters[$propsToUnset]);
            }
        }
        $arrayHydrator = new ArrayHydrator($this->em);
        $entity = $arrayHydrator->hydrateFromArray($entity, $parameters);
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }


    private function createBand()
    {
        return new $this->entityClass();
    }

}