<?php

namespace CTF\ReferralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReferralController extends Controller {

    public function registerAction($ref, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $refid = $this->get('ctf_referral.cryptor')->decrypt($ref);

        $em = $this->getDoctrine()->getEntityManager();
        $refUser = $em->getRepository('CTFUserBundle:User')->find($refid);

        if (null !== $refUser) {
            $user = $this->get('security.context')->getToken()->getUser();

            $refUser->addInvite($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ctf_quest_homepage'));
    }

}
