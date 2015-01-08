<?php
namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class HomeItemsController extends FOSRestController
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
    public function getHomeitemAction($userslug)
    {
        $userId = null;
        try {
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $userId = $user->getId();
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {}

        $data = $this->get('zeba.homeitems_service')->getHomeItems(
            $userId, $this->get('request')->query->get('page', 1), $this->get('request')->query->get('limit', 16)
        );

        $view = $this->view($data, 200);

        return $this->handleView($view);

    }
}