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
    protected $userBandsService;
    public function __construct($cacheProvider,$entityManager,$userBandsService){
        $this->userBandsService = $userBandsService;
        parent::__construct($cacheProvider,$entityManager);
    }
    public function findAll( $page, $limit, $userId)
    {
        $userItems = array();
        $userItems= $this->userBandsService->findAll( $page, $limit,$userId);

        $arrBandIds = array();
        foreach($userItems['bands'] as $userItem){
            $arrBandIds[] = $userItem['id'];
        }
        $userItems['useritems']['id'] = 1;
        $userItems['useritems']['name'] = 'home items test';
        $userItems['useritems']['bands'] = $arrBandIds;
        return $userItems;
    }
    protected function sideloadEntity(&$arrEnt, &$arrAddress, &$arrGenres, &$arrCountries, &$arrCities, &$arrRegions, &$arrAddresses, &$arrInstruments=array())
    {
        $this->sideloadData('genres', $arrEnt, $arrGenres);
        if(isset($arrEnt['instruments'])){
            $this->sideloadData('instruments', $arrEnt,$arrInstruments);
        }
        foreach ($arrEnt['addresses'] as $keyAddress => &$arrAddress) {
            if (!empty($arrAddress['city'])) {
                $this->sideloadData('country', $arrAddress['city'], $arrCountries);
                $arrAddress['country'] = $arrAddress['city']['country'][0];
                unset($arrAddress['city']['country']);
                $this->sideloadData('city', $arrAddress, $arrCities);
                $this->sideloadData('region', $arrAddress, $arrRegions);
            }
        }
        $this->sideloadData('addresses', $arrEnt, $arrAddresses);

    }
} 