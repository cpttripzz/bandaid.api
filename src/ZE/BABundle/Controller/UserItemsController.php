<?php
namespace ZE\BABundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use ZE\BABundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
    public function getUserItemsAction()
    {

        if( !$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            $data = array('Not Authorized');
            $view = $this->view($data, 404);

            return $this->handleView($view);
        }
        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        $data = $this->get('zeba.useritems_service')->findAll($this->get('request')->query->get('page', 1),$this->get('request')->query->get('limit', 12),$userId);

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