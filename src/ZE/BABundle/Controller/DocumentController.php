<?php

namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
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
class DocumentController extends FOSRestController
{

    /**
     * Creates a new Document entity.
     *
     */
    public function postDocumentAction(Request $request, $associationId)
    {

        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository('ZEBABundle:Association')->find($associationId);
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $association)) {
            $view = $this->view(array('not authorized'), 403);
            return $this->handleView($view);
        }
        $entity = new Document();
        $entity->setAssociation($association);
        foreach ($request->files as $file) {
            $entity->setFile($file);
            $em->persist($entity);
        }
        $em->flush();

        $path = $this->get('liip_imagine.cache.manager')->getBrowserPath($entity->getWebPath(), 'assoc');
        $data = array('success' => true, 'path' => $path, 'id' => $entity->getId());
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function deleteDocumentAction($documentId)
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('ZEBABundle:Document')->find($documentId);

        if (false === $this->get('security.authorization_checker')->isGranted('edit', $document->getAssociation())) {
            $view = $this->view(array('not authorized'), 403);
            return $this->handleView($view);
        }
        $em->remove($document);
        $em->flush();

        $data = array('success' => true);
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}
