<?php

namespace CTF\GlobalChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GChatController extends Controller
{
    public function indexAction()
    {
        
        return $this->render('CTFGlobalChatBundle:GChat:index.html.twig');
    }
}
