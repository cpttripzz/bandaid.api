<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/11/14
 * Time: 23:40
 */

namespace ZE\BABundle\Service\Cached;

class InstrumentService extends ServiceAbstract
{
    public function findInstruments($params = array())
    {
        $dql = "
              SELECT i
              FROM ZEBABundle:Instrument i
            ";

        $entitySingular = !empty(array_intersect(array('instrumentId'), array_keys($params)));
        $entityReturnName = 'instruments';
        $query = $this->processQueryPaging($dql, null, null);
        $this->setDqlParams($query, $params, $params);

        return $this->getQueryArrayResult($query, null, null,$entityReturnName,$entitySingular);
    }

} 