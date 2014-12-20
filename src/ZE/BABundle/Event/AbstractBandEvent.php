<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 13/06/14
 * Time: 20:45
 */

namespace ZE\BABundle\Event;


use Symfony\Component\EventDispatcher\Event;

abstract class AbstractBandEvent extends Event
{
    protected $user;
    protected $bandId;
    protected $musicianId;
    protected $eventType;
    const EVENT_TYPE_JOIN = 1;
    const EVENT_TYPE_ACCEPT = 2;
    const EVENT_TYPE_REJECT = 3;

    public function __construct($user,$bandId,$musicianId)
    {
        $this->user = $user;
        $this->bandId = $bandId;
        $this->musicianId = $musicianId;
    }

    /**
     * @return mixed
     */
    public function getBandId()
    {
        return $this->bandId;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getMusicianId()
    {
        return $this->musicianId;
    }
    public function getEventType()
    {
        return $this->eventType;
    }
} 