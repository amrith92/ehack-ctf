<?php

namespace CTF\QuestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HackController extends Controller {
    
    public function minarAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $counts = $em->getRepository('CTFQuestBundle:Stage')->countsPerStage();
        
        $message = array();
        
        foreach ($counts as $k => $v) {
            $message[] = $v[1];
        }
        
        return new Response(\json_encode($message));
    }
}