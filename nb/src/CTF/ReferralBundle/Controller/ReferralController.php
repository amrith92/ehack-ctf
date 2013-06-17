<?php

namespace CTF\ReferralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReferralController extends Controller {
    
    public function refAction($ref, Request $request) {
        if (-1 != $ref) {
            $this->get('session')->set('registration_ref', $ref);
        }
        
        return $this->redirect($this->generateUrl('ctf_quest_homepage'));
    }

    public function registerAction($ref, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (-1 != $ref) {
            $refid = $this->get('ctf_referral.cryptor')->decrypt($ref);

            $em = $this->getDoctrine()->getEntityManager();
            $refUser = $em->getRepository('CTFUserBundle:User')->find($refid);

            if (null !== $refUser) {
                $user = $this->get('security.context')->getToken()->getUser();

                $refUser->addInvite($user);
                $em->merge($refUser);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('ctf_quest_homepage'));
    }

}
