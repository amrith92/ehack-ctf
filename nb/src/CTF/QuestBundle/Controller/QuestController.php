<?php

namespace CTF\QuestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Response;
use CTF\UserBundle\Entity\User;

class QuestController extends Controller
{
    public function dashboardAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setContent(\json_encode("Hello!"));
            return $response;
        }
        $user = $this->get('security.context')->getToken()->getUser();
        if (false == $this->get('ctf_user_util')->hasFullyRegistered($user)) {
            $this->get('session')->getFlashBag()->add('notice', "You've logged in successfully, but your profile isn't quite complete yet! Take a moment to fill in necessary details.");
            return $this->redirect($this->generateUrl('ctf_user_homepage'));
        }
        
        return $this->render('CTFQuestBundle:Quest:dashboard.html.twig');
    }
}
