<?php
namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use ZE\BABundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ZE\BABundle\Request\GetRequest;

class UserItemsController  extends FOSRestController
{
    /**
     * Get content for homepage customized for user
     * if a valid token is passed though http headers handled
     * by symfony security
     *
     * @ApiDoc(
     *  resource=false,
     *  description="Get homepage content",
     *  filters={
     *      {"name"="page", "dataType"="integer"},
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     */
    public function getUseritemAction(Request $request)
    {

        if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
            $data = array('Not Authorized');
            $view = $this->view($data, 404);
            return $this->handleView($view);
        }
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $getRequst = new GetRequest($request->query->all());
        $params = $getRequst->options;
        $params['userId'] = $userId;

        $data = $this->get('zeba.useritems_service')->findAll($params);

        $view = $this->view($data, 200);
        return $this->handleView($view);

    }

    public function showAction(User $user)
    {
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em = $this->getDoctrine()->getManager();

        $bandsOwned = $em->getRepository('ZE\BABundle\Entity\Association')->getAllBandsOwnedByUserId($user->getId());
        $musicianProfiles = $em->getRepository('ZE\BABundle\Entity\Association')->getAllMusiciansOwnedByUserId($user->getId());

        return $this->render(
            'ZEBABundle:User:index.html.twig',array('bands_owned' => $bandsOwned, 'musician_profiles' => $musicianProfiles)

        );
    }

}