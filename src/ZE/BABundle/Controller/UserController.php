<?php

namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use ZE\BABundle\Request\UserRegistrationRequest;

class UserController extends FOSRestController
{
    public function registerAction(Request $request)
    {
        $parameters = $request->request->all();
        $registrationRequest = new UserRegistrationRequest($parameters);
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        $data = $this->get('zeba.user_service')->registerUser($registrationRequest->options,$confirmationEnabled);
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    public function checkUserNameOrEmailAvailableAction(Request $request)
    {
        $parameters = $request->query->all();
        $registrationRequest = new UserRegistrationRequest($parameters);
        $data = $this->get('zeba.user_service')->userExists($registrationRequest->options);
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}