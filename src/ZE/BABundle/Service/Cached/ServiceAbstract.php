<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 22:27
 */
namespace ZE\BABundle\Service\Cached;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Tools\Pagination\Paginator;


class ServiceAbstract
{
    protected $cacheProvider;
    protected $em;
    protected $sideload;

    public function __construct($cacheProvider, $entityManager, $sideload)
    {
        $this->cacheProvider = $cacheProvider;
        $this->em = $entityManager;
        $this->sideload = $sideload;
    }

    protected function setDqlWhere($params, $tableAlias="",$condition='AND')
    {
        $dqlWhere = null;
        foreach ($params as $name => $value) {
            $filter = $tableAlias . '.'. $name. '=' . ':' . $name;
            if (!$dqlWhere) {
                $dqlWhere = ' WHERE ' . $filter;
            } else {
                $dqlWhere .= ' '.$condition .' ' . $filter;
            }
        }
        return $dqlWhere;
    }
    protected function setDqlCustomParamsWhere($dqlParams,$params)
    {
        $dqlWhere =null;
        foreach($dqlParams as $dqlParam => $condition){

            if(!empty($params[$dqlParam])){
                if (!$dqlWhere){
                    $dqlWhere ='WHERE ' . $condition . ':' .$dqlParam;;
                } else {
                    $dqlWhere .= ' AND ' . $condition . ':' . $dqlParam;
                }
            }
        }
        return $dqlWhere;
    }

    protected function setDqlParams($query, $params, $dqlParams)
    {
        foreach ($dqlParams as $dqlParam => $condition) {
            if (!empty($params[$dqlParam])) {
                $query->setParameter($dqlParam, $params[$dqlParam]);
            }
        }
    }

    protected function getCachedByParams($params)
    {
        $key = $this->getKeyFromParams($params);
        return $this->cacheProvider->fetch($key);
    }

    protected function setCachedByParams($params, $data)
    {
        $key = $this->getKeyFromParams($params);
        return $this->cacheProvider->save($key, $data);
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

    public function sideloadData($keyToProcess, &$arrToProcess, &$arrToStoreRelations)
    {
        if (!isset($arrToProcess[$keyToProcess])) {
            return false;
        }
        $arrToStoreIds = array();
        if (is_array($arrToProcess[$keyToProcess])) {
            foreach ($arrToProcess[$keyToProcess] as $key => $arrProcessed) {
                if (is_array($arrProcessed)) {
                    $arrToStoreIds[] = isset($arrProcessed['id']) ? $arrProcessed['id'] : $arrProcessed[0];
                } else {
                    if ($key === 'id') {
                        $arrToStoreIds[] = $arrProcessed;
                    }
                }
            }
        } else {
            return false;
        }
        if (empty($arrToProcess[$keyToProcess][0])) {
            if (empty($arrToProcess[$keyToProcess])) {
                return false;
            }
            $arrToAddToRelations = array($arrToProcess[$keyToProcess]);
        } else {
            $arrToAddToRelations = $arrToProcess[$keyToProcess];
        }
        $arrToStoreRelations = array_merge($arrToStoreRelations, $arrToAddToRelations);
        $arrToStoreRelations = array_map("unserialize", array_unique(array_map("serialize", $arrToStoreRelations)));
        $arrToProcess[$keyToProcess] = $arrToStoreIds;
    }

    /**
     * @param $params
     * @param $dql
     * @return array
     */
    public function getPaginatedArray($dql,$tableAlias, $params=array())
    {
        list($limit, $page, $params) = $this->getPageAndLimit($params);
        $dql .= $this->setDqlWhere($params, $tableAlias , 'OR');
        $query = $this->processQueryPaging($dql, $page, $limit);
        $this->setDqlParams($query, $params, $params);
        return $this->getQueryArrayResult($query, $page, $limit);
    }

    /**
     * @param $query
     * @param $page
     * @param $limit
     * @return array
     */
    protected function getQueryArrayResult($query, $page, $limit, $entityReturnName,$entitySingular=false)
    {
        $query->getArrayResult();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $totalItems = count($paginator);
        $pagesCount = 1;
        if ($page && $limit) {
            $pagesCount = ceil($totalItems / (int)$limit);
        }
        $meta = array('total' => $totalItems, 'pagesCount' => $pagesCount);
        $arrEntity = iterator_to_array($paginator, false);

        if ($entitySingular) {
            $arrEntity = reset($arrEntity);
            return empty($arrEntity) ? array() : reset($arrEntity);
        } else {
            return array($entityReturnName => $arrEntity, 'meta' => $meta);
        }

    }

    /**
     * @param $dql
     * @param $page
     * @param $limit
     * @return mixed
     */
    protected function processQueryPaging($dql, $page, $limit)
    {
        $query = $this->em->createQuery($dql);
        if($page && $limit) {
            $query->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit);
        }
        return $query;
    }

    /**
     * @param $params
     * @return array
     */
    protected function getPageAndLimit($params)
    {
        $page = $limit = 1;
        if (!empty($params['page'])) {
            $page = $params['page'];
            unset($params['page']);
        }
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
            unset($params['limit']);
            return array($limit, $page, $params);
        }
        return array($limit, $page, $params);
    }

}