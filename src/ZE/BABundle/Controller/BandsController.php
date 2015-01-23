<?php

namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use ZE\BABundle\Request\GetBandRequest;
use ZE\BABundle\Request\GetBandsRequest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
    public function getBandsAction(Request $request)
    {
        $getBandsRequest = new GetBandsRequest($request->query->all());
        $data = $this->get('zeba.band_service')->findBands($getBandsRequest->options);
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
    public function getBandAction(Request $request, $slug)
    {
        $data = $this->get('zeba.band_service')->findBands(array('slug' => $slug));
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
            $em = $this->getDoctrine()->getManager();
            $band = $em->getRepository('ZE\BABundle\Entity\Band')->find($id);
            if (false === $this->get('security.authorization_checker')->isGranted('edit', $band)) {
                $view = $this->view(array('not authorized'), 403);
                return $this->handleView($view);
            }
            $this->container->get('zeba_band.handler')->save($request, $id);
            $data = $this->get('zeba.band_service')->findBands(null, null,
                array('bandId' => $id)
            );
            $view = $this->view($data, 200);
            return $this->handleView($view);
        } catch (Exception $e) {
            $view = $this->view(array('form' => $e->getMessage()), 500);

            return $this->handleView($view);
        }
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

    public function postBandAction(Request $request)
    {
        try {
            $entity = $this->container->get('zeba_band.handler')->save($request);
            $id = $entity->getId();
            $data = $this->get('zeba.band_service')->findBands(null, null,
                array('bandId' => $id)
            );
            $view = $this->view($data, 200);
            return $this->handleView($view);
        } catch (Exception $e) {
            $view = $this->view(array('form' => $e->getMessage()), 500);

            return $this->handleView($view);
        }
    }

}
