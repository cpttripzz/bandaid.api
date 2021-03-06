<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;

class GenreService extends ServiceAbstract
{
    public function findGenres($params = array())
    {
        $dql = "
              SELECT g
              FROM ZEBABundle:Genre g
            ";

        $entitySingular = !empty(array_intersect(array('genreId'), array_keys($params)));
        $entityReturnName = 'genres';
        $query = $this->processQueryPaging($dql, null, null);
        $this->setDqlParams($query, $params, $params);

        return $this->getQueryArrayResult($query, null, null,$entityReturnName,$entitySingular);
    }

} 