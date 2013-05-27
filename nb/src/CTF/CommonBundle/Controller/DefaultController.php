<?php

namespace CTF\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CTFCommonBundle:Default:index.html.twig', array('name' => $name));
    }
}
