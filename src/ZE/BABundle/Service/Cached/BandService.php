<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;


use Doctrine\ORM\Tools\Pagination\Paginator;

class BandService extends ServiceAbstract
{
    protected $genreService;

    public function __construct($cacheProvider, $entityManager, $sideload, $genreService)
    {
        $this->genreService = $genreService;
        parent::__construct($cacheProvider, $entityManager, $sideload);
    }

    /**
     * @param $userId
     * @param $page
     * @param $limit
     * @return array
     */
    public function findBands($params = array())
    {
        list($limit, $page, $params) = $this->getPageAndLimit($params);
        $dql = "
              SELECT b, bg, ba, br, bac, bacc, m, mg, mi, ma, mr, mac, macc, bd, md,bu.id AS userId
              FROM ZEBABundle:Band b
              LEFT JOIN b.genres bg
              LEFT JOIN b.addresses ba
              LEFT JOIN ba.region br
              LEFT JOIN ba.city bac
              LEFT JOIN bac.country bacc
              LEFT JOIN b.documents bd
              LEFT JOIN b.musicians m
              LEFT JOIN m.genres mg
              LEFT JOIN m.instruments mi
              LEFT JOIN m.addresses ma
              LEFT JOIN ma.region mr
              LEFT JOIN ma.city mac
              LEFT JOIN mac.country macc
              LEFT JOIN m.documents md
              INNER JOIN b.user bu
            ";

        if (!empty($params['withVacancies'])) {
            $dql .= ' INNER JOIN b.bandVacancyAssociations bva ';
        }
        $entitySingular = !empty(array_intersect(array('bandId', 'slug'), array_keys($params)));
        $entityReturnName = 'bands';

        $dqlParams = array(
            'bandId' => 'b.id = ',
            'slug' => 'b.slug = ',
            'notUser' => 'bu.id != ',
            'userId' => 'bu.id =',
        );

        $dql .= $this->setDqlCustomParamsWhere($dqlParams, $params);
        $query = $this->processQueryPaging($dql, $page, $limit);
        $this->setDqlParams($query, $params, $dqlParams);

        return $this->getQueryArrayResult($query, $page, $limit, $entityReturnName,$entitySingular );

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
    protected function sideloadEntity(&$arrEnt, &$arrAddress, &$arrGenres, &$arrCountries, &$arrCities, &$arrRegions, &$arrAddresses, &$arrDocuments, &$arrInstruments = array())
    {
        $this->sideloadData('genres', $arrEnt, $arrGenres);
        if (isset($arrEnt['instruments'])) {
            $this->sideloadData('instruments', $arrEnt, $arrInstruments);
        }
        foreach ($arrEnt['addresses'] as $keyAddress => &$arrAddress) {
            if (!empty($arrAddress['city'])) {
                $this->sideloadData('country', $arrAddress['city'], $arrCountries);
                $arrAddress['countries'] = array($arrAddress['city']['country'][0]);
                unset($arrAddress['city']['country']);
                $this->sideloadData('city', $arrAddress, $arrCities);
                $this->sideloadData('region', $arrAddress, $arrRegions);
                $arrAddress['cities'] = $arrAddress['city'];
                unset($arrAddress['city']);

                $arrAddress['regions'] = $arrAddress['region'];
                unset ($arrAddress['region']);
            }
        }
        $this->sideloadData('addresses', $arrEnt, $arrAddresses);
        $this->sideloadData('documents', $arrEnt, $arrDocuments);
    }
} 