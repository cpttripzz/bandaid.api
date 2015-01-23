<?php

namespace ZE\BABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use ZE\BABundle\Entity\Band;
use ZE\BABundle\Event\JoinBandAcceptEvent;
use ZE\BABundle\Event\JoinBandRequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiController extends Controller
{
    protected $msgService;
    protected $em;

    public function getTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }
    public function joinBandRequestAction($bandId, $musicianId)
    {
        $this->em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("Not Logged In"), 401);
        }
        if ($user->hasRole('ROLE_USER')) {
            $band = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($bandId);

            if ($this->get('ze.band_manager_service')->isUserInBand($band)) {
                return new JsonResponse(array("success" => false, "msg" => "User Already Member of Band"), 404);
            }
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('zeba.band.join_request', new JoinBandRequestEvent($user, $bandId, $musicianId));
            return new JsonResponse(array("success" => true, "msg" => "Request sent."));
        } else {
            return new JsonResponse(array("success" => false, "msg" => "Not Logged In"), 401);
        }
    }

    public function joinBandAcceptAction($bandId, $musicianId)
    {
        $this->em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("msg" => "Not Logged In"), 401);
        }

        $band = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($bandId);

        if (!$this->get('ze.band_manager_service')->isUserInBand($band)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        if ($user->hasRole('ROLE_USER')) {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('zeba.band.join_accept', new JoinBandAcceptEvent($user, $bandId, $musicianId));
            return new JsonResponse(array("success" => true, "msg" => "User accepted to band."));
        } else {
            return new JsonResponse(array("success" => false, "msg" => "Not Logged In"), 401);
        }
    }

    public function getMessagesAction()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->msgService = $this->get('snc_redis.default');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("success" => false, "msg" => "Not Logged In"), 401);
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $msgIds = $this->msgService->lrange('messages:' . $user->getId(), 0, -1);
        if ($msgIds) {
            $msgs = new \SplFixedArray(count($msgIds));
        }
        $arrCounter = 0;
        foreach ((array)$msgIds as $key => $msgId) {
            $message = $this->msgService->hgetall('message:' . $msgId);
            if ($message) {
                $musician = $this->em->getRepository('ZE\BABundle\Entity\Musician')->findOneById($message['musicianId']);
                $band = $this->em->getRepository('ZE\BABundle\Entity\Band')->findOneById($message['bandId']);
                $musicianUri = $this->generateUrl('musician_show', array('slug' => $musician->getSlug()));
                $bandUri = $this->generateUrl('band_show', array('slug' => $band->getSlug()));
                $acceptUri = $this->generateUrl('api_joinBandAcceptAction',
                    array('bandId' => $message['bandId'], 'musicianId' => $message['musicianId']));

                $message['DT_RowId'] = $msgId;
                $message['counter'] = $arrCounter + 1;
                $musicianLink = '<a href="' . $musicianUri . '">' . $musician->getName() . '</a>';
                $bandLink = '<a href="' . $bandUri . '">' . $band->getName() . '</a';

                $searchArr = array('[musician]', '[band]');
                $replaceArr = array($musicianLink, $bandLink);
                $message['message'] = str_replace($searchArr, $replaceArr, $message['message']);
                if ($message['messageType'] == JoinBandRequestEvent::EVENT_TYPE_JOIN) {
                    $acceptLink = '
                <div><button data-href="' . $acceptUri . '"
                    type="button" class="btn btn-primary">Accept Request</button>
                </div>';

                    $rejectLink = '
                <div><button data-href="' . $acceptUri . '"
                    type="button" class="btn btn-primary">Accept Join Request</button>
                </div>';
                    $message['message'] .= $acceptLink;
                }
                $msgs[$arrCounter] = $message;

                $arrCounter++;
            }
        }
        if (!empty($msgs) && count($msgs)) {
            $msgs = $msgs->toArray();
        } else {
            $msgs = array();
        }
        return new JsonResponse(array("data" => $msgs));

    }

    public function deleteMessagesAction($msgIds)
    {
        $this->msgService = $this->get('snc_redis.default');
        $this->em = $this->getDoctrine()->getManager();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("success" => false, "msg" => "Not Logged In"), 401);
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $arrMsgIds = explode(',', $msgIds);
        $msgIds = $this->msgService->lrange('messages:' . $userId, 0, -1);
        $arrayIntersect = array_intersect($arrMsgIds, $msgIds);
        $this->msgService->decrby('new_messages:' . $userId, count($arrayIntersect));

        foreach ($arrayIntersect as $msgId) {
            $this->msgService->del('message:' . $msgId);
            $this->msgService->lrem('messages:' . $userId, 0, $msgId);
        }
        if (count($arrayIntersect) == 0 || count($arrayIntersect) > 1) {
            $msg = count($arrayIntersect) . " Messages Deleted.";
        } else {
            $msg = "1 Message Deleted.";
        }
        return new JsonResponse(array("success" => true, "msg" => $msg));

    }

    public function saveNewAddressAction($associationId, $associationType, $address)
    {

        $address = $this->get('ze.location_manager_service')->saveNewAddress($associationId, $associationType, $address);
        if($address){
//            $addresses = $this->get('ze.location_manager_service')->getAllAddressesForAssociation($associationId, $associationType);
            $returnObj = array('callback'=>'reloadAddresses','target' => 's2id_ze_babundle_'.$associationType.'_addresses', 'data' => $address);
            return new JsonResponse(array("success" => true, "msg" => 'Address created', 'callback' => $returnObj));
        } else{
            return new JsonResponse(array("success" => false, "msg" => 'Address not created'));

        }


    }

    public function getAllImagesByAssociationIdAction($associationId)
    {
        $images = $this->em->getRepository('ZE\BABundle\Entity\Document')->getAllImagesByAssociationId($associationId);
        return new JsonResponse($images);
    }

    /**
     * Deletes a Document entity.
     *
     */
    public function deleteDocumentsAction($ids, $associationId)
    {
        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository('ZE\BABundle\Entity\Association')->find($associationId);


        if (false === $this->get('security.authorization_checker')->isGranted('edit', $association)) {
            return new JsonResponse('not authorized', 403);
        }
        $ids = explode(',',$ids);
        $ids = array_flip($ids);
        foreach ($association->getDocuments() as $document) {
            if (isset ($ids[$document->getId()])) {
                $em->remove($document);
                $em->flush();

            }
        }

        return new JsonResponse('Successfully Removed');


    }
}
