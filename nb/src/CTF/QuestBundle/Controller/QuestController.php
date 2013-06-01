<?php

namespace CTF\QuestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Response;

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
        return $this->render('CTFQuestBundle:Quest:dashboard.html.twig');
    }
}
