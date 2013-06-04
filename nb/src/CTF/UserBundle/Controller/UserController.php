<?php

namespace CTF\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CTF\UserBundle\Form\UserType;

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

}
