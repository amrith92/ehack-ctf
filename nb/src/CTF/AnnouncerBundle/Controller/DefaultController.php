<?php

namespace CTF\AnnouncerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CTFAnnouncerBundle:Default:index.html.twig', array('name' => $name));
    }
}
