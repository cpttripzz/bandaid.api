<?php
namespace ZE\BABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use ZE\BABundle\Event\JoinBandRequestEvent;

class MessageController extends Controller
{
    protected $msgService;

    public function indexAction(Request $request){

        return $this->render(
            'ZEBABundle:Message:index.html.twig'
        );
    }
}