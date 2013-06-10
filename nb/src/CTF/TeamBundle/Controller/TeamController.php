<?php

namespace CTF\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CTF\SecurityBundle\Exception\AccessDeniedException;
use CTF\TeamBundle\Form\TeamSelectType;

class TeamController extends Controller {
    
    public function selectAction() {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        //$user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new TeamSelectType());
        
        return $this->render('CTFTeamBundle:Team:select-team.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
