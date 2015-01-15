<?php

namespace ZE\BABundle\Security\Authentication\EventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener {
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user ) {
            return;
        }

        // $data['token'] contains the JWT

        $data['username'] = $user->getUsername();
        $data['userId'] = $user->getId();
        $data['roles'] = $user->getRoles();
        $event->setData($data);
    }
} 