<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;

class HomeItemsService extends ServiceAbstract
{
    protected $bandService;
    public function __construct($cacheProvider,$entityManager,$sideload,$bandService){
        $this->bandService = $bandService;
        parent::__construct($cacheProvider,$entityManager,$sideload);
    }
    public function getHomeItems($userId, $page, $limit)
    {
        $homeItems = array();
        $homeItems= $this->bandService->findBands( $page, $limit,$userId,true);

        $arrBandIds = array();

        $homeItems['homeitem']['id'] = 1;
        $homeItems['homeitem']['name'] = 'home items test';
        if($this->sideload){
            foreach($homeItems['bands'] as $key=>$homeItem){
                $arrBandIds[] = $homeItem['id'];
            }
            $homeItems['homeitem']['bands'] = $arrBandIds;
        }

        return $homeItems;
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