<?php

namespace CTF\CertificateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \CTF\CertificateBundle\Entity\CertifyData;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CertificateController extends Controller {
    
    public function indexAction($mode) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $generator = $this->get('ctf_certificate.generator');
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CTFQuestBundle:UserQuest');
        $userquest = $repo->findByUser($user);
        $rank = $repo->getRankByUser($user->getId());
        $team = $em->getRepository('CTFTeamBundle:Team')->findAcceptedRequestByUserId($user->getId());
        
        $data = new CertifyData();
        $data->setFullName($user->getFname() . ' ' . $user->getLname());
        $data->setOrganization(\explode(', ', $user->getOrg()->getName())[0]);
        $data->setScore($userquest->getScore());
        $data->setRank($rank);
        $serial = \sprintf("%06d", $user->getId());
        $data->setSerial('CTFEHACK-' . $serial);
        $data->setTeam($team);
        $data->setTimestamp(new \DateTime(date('Y-m-d H:i:s')));
        
        if ('png' === $mode) {
            $response = new Response($generator->generatePngCertificate($data), 200, array(
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename=EHACK-certificate.png'
            ));
        } else {
            $response = new Response($generator->generatePdfCertificate($data), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=EHACK-certificate.pdf',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public'
            ));
        }
        
        return $response;
    }
    
    public function userAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('CTFUserBundle:User')->find($id);
        
        if (null != $user) {
            $generator = $this->get('ctf_certificate.generator');
            $repo = $em->getRepository('CTFQuestBundle:UserQuest');
            $userquest = $repo->findByUser($user);
            $rank = $repo->getRankByUser($user->getId());
            $team = $em->getRepository('CTFTeamBundle:Team')->findAcceptedRequestByUserId($user->getId());

            $data = new CertifyData();
            $data->setFullName($user->getFname() . ' ' . $user->getLname());
            $data->setOrganization(\explode(', ', $user->getOrg()->getName())[0]);
            $data->setScore($userquest->getScore());
            $data->setRank($rank);
            $serial = \sprintf("%06d", $user->getId());
            $data->setSerial('CTFEHACK-' . $serial);
            $data->setTeam($team);
            $data->setTimestamp(new \DateTime(date('Y-m-d H:i:s')));

            $response = new Response($generator->generatePngCertificate($data), 200, array(
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename=EHACK-certificate.png'
            ));

            return $response;
        }
        
        return new Response('Bad Request!', 400);
    }
}
