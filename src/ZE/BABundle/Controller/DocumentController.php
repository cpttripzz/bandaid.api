<?php

namespace ZE\BABundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ZE\BABundle\Entity\Association;
use ZE\BABundle\Entity\Document;
use ZE\BABundle\Form\DocumentType;

/**
 * Document controller.
 *
 */
class DocumentController extends Controller
{

    /**
     * Lists all Document entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ZEBABundle:Document')->findAll();

        return $this->render('ZEBABundle:Document:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function createAction(Request $request, $aid)
    {
        $entity = new Document();
        $form = $this->createCreateForm($entity,$aid);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository('ZEBABundle:Association')->find($aid);
        $entity->setAssociation($association);
//        if ($form->isValid()) {
        if(true){
            foreach($request->files as $file){
                $entity->setFile($file);
                $em->persist($entity);
            }
            $em->flush();

        }
        if ($request->isXmlHttpRequest()) {
            $path = $this->get('liip_imagine.cache.manager')->getBrowserPath($entity->getWebPath(), 'assoc');
            return new JsonResponse(array('success' => true, 'path' => $path, 'id' => $entity->getId()));
        }
        return $this->render('ZEBABundle:Document:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Document entity.
     *
     * @param Document $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Document $entity, $aid)
    {

        $form = $this->createForm(new DocumentType(), $entity, array(
            'action' => $this->generateUrl('document_create',array('aid' => $aid) ),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Document entity.
     *
     */
    public function newAction($aid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Document();
        $em->persist($entity);
        $em->flush();
        $form = $this->createCreateForm($entity,  array('aid' => $aid));

        return $this->render('ZEBABundle:Document:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZEBABundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $entity->getAssociation()->getId());

        return $this->render('ZEBABundle:Document:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZEBABundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZEBABundle:Document:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Document entity.
     *
     * @param Document $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Document $entity)
    {
        $form = $this->createForm(new DocumentType(), $entity, array(
            'action' => $this->generateUrl('document_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Document entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZEBABundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('document_edit', array('id' => $id)));
        }

        return $this->render('ZEBABundle:Document:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Document entity.
     *
     */
    public function deleteAction(Request $request, $id, $associationId)
    {
        $form = $this->createDeleteForm($id, $associationId);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository('ZE\BABundle\Entity\Association')->find($associationId);


        if (false === $this->get('security.context')->isGranted('edit', $association)) {
            return new JsonResponse('not authorized', 403);
        }

        foreach ($association->getDocuments() as $document) {
            if ($document->getId() == $id) {
                $em->remove($document);
                $em->flush();
                return new JsonResponse('Successfully Removed');
            }
        }

        return new JsonResponse('Unable to find Document entity.', 500);


    }

    /**
     * Creates a form to delete a Document entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('document_delete'))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
