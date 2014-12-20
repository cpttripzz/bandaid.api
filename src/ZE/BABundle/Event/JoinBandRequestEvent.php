<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 13/06/14
 * Time: 20:45
 */

namespace ZE\BABundle\Event;


use Symfony\Component\EventDispatcher\Event;

class JoinBandRequestEvent extends AbstractBandEvent
{

    public function __construct($user,$bandId, $musicianId)
    {
        parent::__construct($user,$bandId, $musicianId);
        $this->eventType = self::EVENT_TYPE_JOIN;
    }

} 