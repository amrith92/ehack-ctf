<?php

namespace CTF\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    public function facebookChannelAction()
    {
        $cache_expire = 60 * 60 * 24 * 365;
        $response = new Response();
        $response->setPublic();
        $response->setExpires(\DateTime::createFromFormat('D, d M Y H:i:s', \gmdate('D, d M Y H:i:s', \time() + $cache_expire)));
        $response->setContent($this->renderView('CTFCommonBundle:Channel:channel.html.twig'));
    }
}