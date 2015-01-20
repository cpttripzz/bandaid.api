<?php

namespace ZE\BABundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ZE\BABundle\Entity\Band;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ZE\BABundle\Exception\InvalidFormException;
use ZE\BABundle\Request\AdminUsersRequest;


class AdminController extends FOSRestController
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
    public function getUserreportsAction(Request $request)
    {
        $request = new AdminUsersRequest($request->query->all());
        $data = $this->get('zeba.admin_service')->findUsers($request->options);

        $view = $this->view($data, 200);

        return $this->handleView($view);

    }
}
