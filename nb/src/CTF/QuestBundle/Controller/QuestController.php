<?php

namespace CTF\QuestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QuestController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('CTFQuestBundle:Quest:dashboard.html.twig');
    }
}
