<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;

class UserItemsService extends ServiceAbstract
{
    protected $bandService;
    public function __construct($cacheProvider,$entityManager,$sideload,$userBandsService){
        $this->bandService = $userBandsService;
        parent::__construct($cacheProvider,$entityManager,$sideload);
    }
    public function findAll( $params)
    {
        $userItems = array();
        $userItems = $this->bandService->findBands($params);
        return $userItems;
    }
} 