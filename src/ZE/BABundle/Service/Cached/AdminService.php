<?php

namespace ZE\BABundle\Service\Cached;

class AdminService extends ServiceAbstract
{
    protected $userService;
    public function __construct($cacheProvider, $entityManager, $sideload)
    {
        parent::__construct($cacheProvider, $entityManager, $sideload);
    }
} 