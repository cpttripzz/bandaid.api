<?php

namespace ZE\BABundle\Service\Cached;

use ZE\BABundle\Util\RestUtil;

class UserService extends ServiceAbstract
{
    protected $userService;
    protected $columnsToExpose = array('id', 'email','username','enabled','roles');
    public function __construct($cacheProvider,$entityManager,$sideload,$userService){
        $this->userService = $userService;
        parent::__construct($cacheProvider,$entityManager,$sideload);
    }

    public function userExists($params)
    {
        if(isset($params['password'])){
            unset ($params['password']);
        }
        $user = $this->findUsers($params);
        return ! empty($user['users']);
    }

    public function registerUser($params, $confirmationEnabled)
    {
        if($this->userExists($params)){
            return RestUtil::formatRestResponse(false,'Username or email already in use');
        } else {
            $user = $this->userService->createUser();
            $user->setUsername($params['username']);
            $user->setEmail($params['email']);
            $user->setPlainPassword($params['password']);

            $user->setEnabled(!$confirmationEnabled);
            $user->addRole('ROLE_USER');
            $this->userService->updateUser($user, true);
            $this->em->flush();

            $newId = $user->getId();
            $user = $this->findUsers(array('id' => $newId));

            if(!empty($user['users'])) {
                $user = array_intersect_key($user['users'][0], array_flip($this->columnsToExpose));
                return $user;
            } else {
                return RestUtil::formatRestResponse(false,'Error registering user');
            }

        }
    }
    /**
     * @param $userId
     * @param $page
     * @param $params['limit']
     * @return array
     */
    public function findUsers($params = array())
    {
        $dql = "
              SELECT u
              FROM ZEBABundle:User u
            ";

        list($meta, $arrEntity) = $this->getPaginatedArray($dql,'u',$params);
        return array('users' => $arrEntity, 'meta' => $meta);
    }
}