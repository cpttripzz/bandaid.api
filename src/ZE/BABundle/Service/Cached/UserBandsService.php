<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;


use Doctrine\ORM\Tools\Pagination\Paginator;

class UserBandsService extends ServiceAbstract
{
    protected $genreService;

    public function __construct($cacheProvider,$entityManager,$sideload,$genreService){
        $this->genreService = $genreService;
        parent::__construct($cacheProvider,$entityManager,$sideload);
    }
    /**
     * @param $userId
     * @param $page
     * @param $limit
     * @return array
     */
    public function findAll($page, $limit,$userId)
    {
        if(!(int) $page){
            $page = 1;
        }


            $dql = "
              SELECT b, bg, ba, br, bac, bacc, bd, bu.id AS userId
              FROM ZEBABundle:Band b
              LEFT JOIN b.genres bg
              LEFT JOIN b.addresses ba
              LEFT JOIN ba.region br
              LEFT JOIN ba.city bac
              LEFT JOIN bac.country bacc
              LEFT JOIN b.documents bd
              INNER JOIN b.user bu
              WHERE bu.id = :userId
            ";

            $query = $this->em->createQuery($dql)
                ->setFirstResult(($page-1) * $limit )
                ->setMaxResults($limit)
                ->setResultCacheDriver($this->cacheProvider)
                ->setResultCacheLifetime(86400)
                ->setParameter('userId', $userId)
            ;
            $query->getArrayResult();
            $paginator = new Paginator($query, $fetchJoinCollection = true);
            $totalItems = count($paginator);
            $pagesCount = ceil($totalItems / $limit);
            $arrEntity = iterator_to_array($paginator,false);
            $arrGenres = $arrAddresses = $arrCities = $arrCountries = $arrRegions = $arrDocuments = array();

        if($this->sideload) {

            foreach ($arrEntity as $keyEntity => &$arrEnt) {
                $this->sideloadEntity($arrEnt, $arrAddress, $arrGenres, $arrCountries, $arrCities, $arrRegions, $arrAddresses, $arrDocuments);
            }
        }
        $meta = array('total'=>$totalItems,'pagesCount'=>$pagesCount);
        $arrGenres = $this->genreService->findGenres();

        if($this->sideload){
            $arrGenres = $arrGenres['genres'];
            return array(
                'bands' => $arrEntity,'genres' =>$arrGenres,'countries' =>$arrCountries,
                'regions' => $arrRegions,'cities' =>$arrCities,'addresses' => $arrAddresses,
                'documents' => $arrDocuments, 'meta' =>$meta
            );
        } else {
            return array(
                'bands' => $arrEntity,'genres' =>$arrGenres,
                'meta' => $meta,
            );
        }


    }



    /**
     * @param $arrEnt
     * @param $arrAddress
     * @param $arrGenres
     * @param $arrCountries
     * @param $arrCities
     * @param $arrRegions
     * @param $arrAddresses
     * @param array $arrInstruments
     */
    protected function sideloadEntity(&$arrEnt, &$arrAddress, &$arrGenres, &$arrCountries, &$arrCities, &$arrRegions, &$arrAddresses, &$arrDocuments, &$arrInstruments=array())
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
        $this->sideloadData('documents', $arrEnt, $arrDocuments);
    }
} 