<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 13/06/14
 * Time: 20:50
 */

namespace ZE\BABundle\EventListener;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use ZE\BABundle\Event\JoinBandRequestEvent;
use  React\ZMQ\Context;

class JoinBandRequestEventListener
{
    protected $msgService;
    protected $em;

    public function __construct($em, $msgService)
    {
        $this->em = $em;
        $this->msgService = $msgService;
    }

    public function onJoinBandRequestEvent(JoinBandRequestEvent $event)
    {
        $requestingUser = $event->getUser();
        $requestingUserId = $requestingUser->getId();
        $musicianId = $event->getMusicianId();
        $bandId = $event->getBandId();
        $band = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($bandId);
        $bandName = $band->getName();
        $eventType = $event->getEventType();
        $bandMembers = $band->getMusicians($bandId);

        $msgRecipients = array();
        foreach ($bandMembers as $bandMember) {
            $userId = $bandMember->getUser()->getId();

            $now = new \DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $nextMessageId = $this->msgService->incr('next_message_id');
            $this->msgService->hmset(
                'message:' . $nextMessageId,
                'musicianId', $musicianId,
                'bandId', $bandId,
                'messageType', $eventType,
                'sent', $now,
                'message', 'Musician [musician] requested to join band [band]'
            );
            $this->msgService->rpush('messages:' . $userId, $nextMessageId);
            $numNewMessages = $this->msgService->incr('new_messages:' . $userId);
            $msgRecipients[$userId] = $numNewMessages;
        }
        $this->msgService->publish('realtime', json_encode($msgRecipients));
        $nextMessageId = $this->msgService->incr('publisher_count');
    }


}