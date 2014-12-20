<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 13/06/14
 * Time: 20:50
 */

namespace ZE\BABundle\EventListener;

use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ZE\BABundle\Controller\UrlTracker;
class SetUrlEventListener
{
    /** @var \Symfony\Component\Routing\Router  */
    protected $router;
    /** @var Symfony\Component\HttpFoundation\Session\ */
    protected $session;

    public function __construct(Router $router)
    {
        $this->router = $router;

    }
    public function setSession($session=null)
    {
        $this->session = $session;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }


        if($this->session){
            if ($controller[0] instanceof UrlTracker) {
                $this->session->set ('currentUrl', $event->getRequest()->getPathInfo());
            }

        }
    }
} 