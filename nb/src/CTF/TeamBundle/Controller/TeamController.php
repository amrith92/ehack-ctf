<?php

namespace CTF\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CTF\SecurityBundle\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller {
    
    public function selectAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        //$user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm($this->get('ctf.form.select_team'));
        
        return $this->render('CTFTeamBundle:Team:select-team.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function selectAjaxAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm($this->get('ctf.form.select_team'));
        
        if (true === $request->isMethod('POST') && $request->isXmlHttpRequest()) {
            // process select-team form
            $form->bind($request);
            
            return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }
    
    public function selectTeamAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $form = $this->createForm($this->get('ctf.form.select_team'));
        
        if (true === $request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                $dto = $form->getData();
                
                if ('create' == $dto->getIsSelecting()) {
                    return $this->render('CTFUserBundle:User:debug.html.twig', array(
                        'debug' => $dto->getTeam()
                    ));
                } else {
                    
                }
            }
        }
    }
}
