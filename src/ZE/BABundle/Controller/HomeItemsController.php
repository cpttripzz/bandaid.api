<?php
namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use ZE\BABundle\Request\GetRequest;

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
    public function getHomeitemsAction(Request $request)
    {
        $userId = null;
        try {
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $userId = $user->getId();
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {}
        $getRequst = new GetRequest($request->query->all());
        $params = $getRequst->options;
        $params['userNot'] = $userId;
        $data = $this->get('zeba.homeitems_service')->getHomeItems($params);

        $view = $this->view($data, 200);

        return $this->handleView($view);

    }
}