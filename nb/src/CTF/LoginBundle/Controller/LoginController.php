<?php

namespace CTF\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function checkAction()
    {
        return new Response(
            ($this->get('security.context')->isGranted('ROLE_USER') === true) ?
                'true' : 'false'
        );
    }
}
