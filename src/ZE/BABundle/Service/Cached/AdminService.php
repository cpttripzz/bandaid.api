<?php

namespace ZE\BABundle\Service\Cached;

class AdminService extends ServiceAbstract
{
   public function __construct($cacheProvider, $entityManager, $sideload)
    {
        parent::__construct($cacheProvider, $entityManager, $sideload);
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

        list($meta, $arrEntity) = $this->getPaginatedArray($params, $dql);
        return array('users' => $arrEntity, 'meta' => $meta);
    }
} 