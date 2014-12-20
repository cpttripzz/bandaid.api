<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 22:27
 */
namespace ZE\BABundle\Service\Cached;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;

class ServiceAbstract
{
    protected $cacheProvider;
    protected $em;

    public function __construct($cacheProvider,$entityManager){
        $this->cacheProvider = $cacheProvider;
        $this->em = $entityManager;
    }

    protected function getCachedByParams($params)
    {
        $key = $this->getKeyFromParams($params);
        return $this->cacheProvider->fetch($key);
    }
    protected function setCachedByParams($params,$data)
    {
        $key = $this->getKeyFromParams($params);
        return $this->cacheProvider->save($key,$data);
    }


    /**
     * @param $params
     * @return int|string
     */
    protected function getKeyFromParams($params)
    {
        ksort($params);
        $key = '';
        foreach ($params as $k => $value) {
            if ($value) {
                $key .= $k . '-' . $value;
            }
        }
        $key = md5(__METHOD__ . $key);
        return $key;
    }

    public function sideloadData($keyToProcess,&$arrToProcess, &$arrToStoreRelations){
        if(!isset($arrToProcess[$keyToProcess])){
            return false;
        }
        $arrToStoreIds = array();
        if(is_array($arrToProcess[$keyToProcess])) {
            foreach ($arrToProcess[$keyToProcess] as $key =>$arrProcessed) {
                if(is_array($arrProcessed)) {
                    $arrToStoreIds[] = isset($arrProcessed['id']) ? $arrProcessed['id']: $arrProcessed[0];
                } else {
                    if($key==='id'){
                        $arrToStoreIds[] = $arrProcessed;
                    }
                }
            }
        } else {
            return false;
        }
        if(empty($arrToProcess[$keyToProcess][0])){
            if ( empty($arrToProcess[$keyToProcess])){
                return false;
            }
            $arrToAddToRelations = array($arrToProcess[$keyToProcess]);
        } else {
            $arrToAddToRelations = $arrToProcess[$keyToProcess];
        }
        $arrToStoreRelations = array_merge($arrToStoreRelations,$arrToAddToRelations);
        $arrToStoreRelations = array_map("unserialize", array_unique(array_map("serialize", $arrToStoreRelations)));
        $arrToProcess[$keyToProcess] = $arrToStoreIds;

    }

}