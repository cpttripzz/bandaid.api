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
use ZE\BABundle\Event\JoinBandAcceptEvent;
use ZE\BABundle\Entity\BandMusician;

class JoinBandAcceptEventListener
{
    protected $msgService;
    protected $bandManager;
    protected $em;

    public function __construct($em,$msgService,$bandManagerService)
    {
        $this->em = $em;
        $this->msgService = $msgService;
        $this->bandManager = $bandManagerService;
    }

    public function onJoinBandAcceptEvent(JoinBandAcceptEvent $event)
    {
        $user = $event->getUser();
        $userId = $user->getId();
        $bandId = $event->getBandId();
        $band = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($bandId);
        $bandName = $band->getName();
        $musicianId = $event->getMusicianId();
        $musician = $this->em->getRepository('ZE\BABundle\Entity\Musician')->findOneById($musicianId);
        $eventType = $event->getEventType();
        $recipientId = $musician->getUser()->getId();
        $this->bandManager->addMusicianToBand($musician,$band);
        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $nextMessageId = $this->msgService->incr('next_message_id');

        $this->msgService->hmset(
            'message:' . $nextMessageId,
            'musicianId', $userId,
            'bandId', $bandId,
            'messageType', $eventType,
            'sent', $now,
            'message', 'You are now a member of band [band]'
        );
        $this->msgService->rpush('messages:' . $recipientId, $nextMessageId);
        $numNewMessages = $this->msgService->incr('new_messages:' . $recipientId);
        $msgRecipients[$recipientId] = $numNewMessages;
        $this->msgService->publish('realtime', json_encode($msgRecipients));
    }


}