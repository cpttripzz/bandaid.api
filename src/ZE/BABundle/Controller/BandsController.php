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

}
