<?php
namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use ZE\BABundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserBandsController  extends FOSRestController
{
    /**
     * Get content for homepage customized for user
     * if a valid token is passed though http headers handled
     * by symfony security
     *
     * @ApiDoc(
     *  resource=false,
     *  description="Get homepage content",
     *  filters={
     *      {"name"="page", "dataType"="integer"},
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     */
    public function getUserBandsAction()
    {

        if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
            $data = array('Not Authorized');
            $view = $this->view($data, 404);

            return $this->handleView($view);
        }
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $data = $this->get('zeba.userbands_service')->findAll($this->get('request')->query->get('page', 1),$this->get('request')->query->get('limit', 12),$userId);

        $view = $this->view($data, 200);
        return $this->handleView($view);

    }

}