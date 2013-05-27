<?php

namespace CTF\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CTFLoginBundle:Default:index.html.twig');
    }
}
