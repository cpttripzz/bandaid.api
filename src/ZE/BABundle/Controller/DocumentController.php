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
    public function postDocumentAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $data = array('Not Authorized');
            $view = $this->view($data, 404);
            return $this->handleView($view);
        }
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $entity = new Document();
        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository('ZEBABundle:Association')->find($userId);
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
}
