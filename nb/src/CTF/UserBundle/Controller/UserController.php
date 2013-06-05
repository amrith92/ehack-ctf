<?php

namespace CTF\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CTF\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

    public function profileAction() {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        if (false === $this->get('ctf_user_util')->hasFullyRegistered($user)) {
            if ('google' === $user->getLoginMode()) {
                if (true === $this->get('ctf_user_util')->populateWithGooglePlus($user)) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->merge($user);
                    $em->persist($user);
                    $em->flush();
                }
                /*return $this->render('CTFUserBundle:User:debug.html.twig', array(
                   'debug' => $this->get('ctf_user_util')->populateWithGooglePlus($user)
                ));*/
            }
        }
        
        $form = $this->createForm(new UserType());
        $form->setData($user);

        return $this->render('CTFUserBundle:User:profile.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function getCountriesAction() {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $countries = $this->getDoctrine()->getRepository('CTFUserBundle:Countries')->findAll();
            
            $retarr = array();
            foreach ($countries as $v) {
                $retarr[] = array('id' => $v->getId(), 'name' => $v->getName());
            }
            
            return new Response(\json_encode($retarr));
        }
        
        return new Response('NOT ALLOWED!');
    }
    
    public function getStatesAction($id) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $zones = $this->getDoctrine()->getRepository('CTFUserBundle:Zone')->findStatesByCountryId($id);
            
            $retarr = array();
            foreach ($zones as $v) {
                $retarr[] = array('id' => $v->getId(), 'name' => $v->getName());
            }
            
            if (empty($retarr)) {
                $retarr[] = array('id' => -99, 'name' => 'Not Applicable');
            }
            
            return new Response(\json_encode($retarr));
        }
        
        return new Response('NOT ALLOWED!');
    }

}
