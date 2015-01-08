<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;


use Doctrine\ORM\Tools\Pagination\Paginator;

class GenreService extends ServiceAbstract
{


    public function findGenres($page = null, $limit = null, $params = array())
    {

        $dql = "
              SELECT g
              FROM ZEBABundle:Genre g
            ";

        $query = $this->em->createQuery($dql)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $query->getArrayResult();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $totalItems = count($paginator);
        $pagesCount = 1;
        if ($page && $limit) {
            $pagesCount = ceil($totalItems / (int)$limit);
        }
        $arrEntity = iterator_to_array($paginator, false);


        $meta = array('total' => $totalItems, 'pagesCount' => $pagesCount);
        if ($this->sideload) {
            return array(
                'genres' => $arrEntity,
                'meta' => $meta,
            );
        } else {
            return $arrEntity;
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