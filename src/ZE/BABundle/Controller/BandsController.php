<?php

namespace ZE\BABundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ZE\BABundle\Entity\Band;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ZE\BABundle\Exception\InvalidFormException;


class BandsController extends FOSRestController
{

    /**
     * Get content for homepage customized for user
     * if a valid token is passed though http headers handled
     * by symfony security
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get homepage content",
     *  filters={
     *      {"name"="page", "dataType"="integer"},
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     */
    public function getBandsAction()
    {

        $data = $this->get('zeba.band_service')->findBands(
            $this->get('request')->query->get('page', 1),
            $this->get('request')->query->get('limit', 12)
        );

        $view = $this->view($data, 200);

        return $this->handleView($view);

    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Specific Band Info",
     *  filters={
     *      {"name"="slug", "dataType"="string"},
     *  }
     * )
     */
    public function getBandAction($slug)
    {

        $data = $this->get('zeba.band_service')->findBands(
            $this->get('request')->query->get('page', 1),
            $this->get('request')->query->get('limit', 12),
            array('bandSlug'=>$slug)
        );

        $view = $this->view($data, 200);

        return $this->handleView($view);

    }
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update Band Info",
     *  filters={
     *      {"name"="id", "dataType"="integer"},
     *  }
     * )
     */

    public function putBandAction(Request $request, $id)
    {
        try {

            $this->container->get('zeba_band.handler')->post($request,$id);
            $data = $this->get('zeba.band_service')->findBands(null,null,
                array('bandId'=>$id)
            );
            $view = $this->view($data, 200);
            return $this->handleView($view);
        } catch (Exception $e){
            $view = $this->view(array('form' => $e->getMessage()), 500);

            return $this->handleView($view);
        }


    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT b FROM ZEBABundle:Band b";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1), 16
        );
        $pagination->setTemplate('KnpPaginatorBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig');
        return $this->render('ZEBABundle:Band:index.html.twig', array('pagination' => $pagination, 'entity_type' => 'band'));
    }

    public function createAction(Request $request)
    {
        $entity = new Band();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('band_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Band entity.
     *
     * @param Band $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Band $entity)
    {
        $form = $this->createForm(new BandType($this->get('security.context')), $entity, array(
            'show_legend' => false,
            'action' => $this->generateUrl('band_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


    public function newAction()
    {
        $entity = new Band();
        $form = $this->createCreateForm($entity);

        return $this->render('ZEBABundle:Band:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }


    public function showAction(Band $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Band entity.');
        }
        $userInBand = $this->get('ze.band_manager_service')->isUserInBand($entity);
        return $this->render('ZEBABundle:Band:show.html.twig', array(
            'userInBand' => $userInBand, 'entity' => $entity
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZEBABundle:Band')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Band entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZEBABundle:Band:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Band entity.
     *
     * @param Band $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Band $entity)
    {
        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $form = $this->createForm(new BandType($this->get('security.context')), $entity, array(
            'action' => $this->generateUrl('band_update', array('id' => $entity->getId())),
            'show_legend' => false,
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZEBABundle:Band')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Band entity.');
        }
        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $originalBandVacancyAssociations = new ArrayCollection();
        $originalBandMedia = new ArrayCollection();
        foreach ($entity->getBandVacancyAssociations() as $bandVacancyAssociation) {
            $originalBandVacancyAssociations->add($bandVacancyAssociation);
        }



        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            foreach ($originalBandVacancyAssociations as $bandVacancyAssociation) {
                if (false === $entity->getBandVacancyAssociations()->contains($bandVacancyAssociation)) {
                    $entity->getBandVacancyAssociations()->removeElement($bandVacancyAssociation);
                    $em->persist($entity);
                    $em->remove($bandVacancyAssociation);
                }
            }


            $em->persist($entity);


            $em->flush();

            return $this->redirect($this->generateUrl('band_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ZEBABundle:Band')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Band entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('band'));
    }

    /**
     * Creates a form to delete a Band entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('show_legend' => false))
            ->setAction($this->generateUrl('band_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
