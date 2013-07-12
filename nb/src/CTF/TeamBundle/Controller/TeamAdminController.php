<?php

namespace CTF\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamAdminController extends Controller {
    
    public function teamAdminAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        // Extra-security so that only admins can access the team-actions
        $user = $this->get('security.context')->getToken()->getUser();
        
        if (null === $this->get('session')->get('team_admin_auth')) {
            $salt = $this->container->getParameter('secret');
            $this->get('session')->set('team_admin_auth', md5($salt . $user->getId() . $salt));
        }
        
        // Figure out which team to adminify
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $cache = $this->get('ctf_cache');
        
        $teamid = $cache->get(\md5($user->getId() . '_teamid'));
        
        if (false === $teamid) {
            $teamid = $teamrepo->findAdminedByUserId($user->getId());
            $cache->store(\md5($user->getId() . '_teamid'), $teamid, 172800);
            $cache->store(\md5($user->getId() . '_last_access_team_admin_panel'), new \DateTime("now"));
        }
        
        $team = $teamrepo->find($teamid);
        
        $response = new Response();
        $response->setEtag($team->computeETag());
        $lastModified = $cache->get(\md5($user->getId() . '_last_access_team_admin_panel'), new \DateTime("now"));
        if (false !== $lastModified) {
            $response->setLastModified($lastModified);
        }
        $response->setPublic();
        
        if ($response->isNotModified($request)) {
            return $response;
        } else {
            return $this->render('CTFTeamBundle:TeamAdmin:teamadmin.html.twig', array(
                'team' => $team
            ), $response);
        }
    }
    
    private function isTeamAdminTokenValid() {
        $user = $this->get('security.context')->getToken()->getUser();
        $salt = $this->container->getParameter('secret');
        return ($this->get('session')->get('team_admin_auth') === md5($salt . $user->getId() . $salt));
    }
    
    public function acceptAction($tid, $rid, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (!$this->isTeamAdminTokenValid()) {
            return new Response('Bad Request!', 400);
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $teamid = $teamrepo->findAdminedByUserId($user->getId());
        
        if ($teamid === null || $teamid != $tid) {
            return new Response('Bad Request!', 400);
        }
        
        $team = $teamrepo->find($tid);
        
        $requests = $team->getRequests();
        $ctr = 0;
        foreach ($requests as $r) {
            if ($r->getStatus() == TeamRequestStatus::$ACCEPTED || $r->getStatus() == TeamRequestStatus::$ACCEPTEDANDADMIN) {
                ++$ctr;
            }
        }
        
        $max = (int) $this->container->getParameter('max_per_team');
        if ($ctr < $max) {
            foreach ($requests as $r) {
                if ($r->getId() == $rid) {
                    // Check if the user is already a part of the team
                    // BEFORE accepting
                    $existing = $teamrepo->findAcceptedRequestByUserId($r->getUser()->getId());
                    if ($existing == null || false == $existing) {
                        $r->setStatus(TeamRequestStatus::$ACCEPTED);
                    } else {
                        $this->get('session')->getFlashBag()->add('error', "User is already part of another team!");
                        return $this->redirect($this->generateUrl('ctf_team_admin'));
                    }
                    break;
                }
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Successfully accepted user!");
        } else {
            $this->get('session')->getFlashBag()->add('error', "You have reached the maximum number of members for teams [" . $max . "]");
        }
        
        return $this->redirect($this->generateUrl('ctf_team_admin'));
    }
    
    public function rejectAction($tid, $rid, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (!$this->isTeamAdminTokenValid()) {
            return new Response('Bad Request!', 400);
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $teamid = $teamrepo->findAdminedByUserId($user->getId());
        
        if ($teamid === null || $teamid != $tid) {
            return new Response('Bad Request!', 400);
        }
        
        $team = $teamrepo->find($tid);
        $cache = $this->get('ctf_cache');
        
        $requests = $team->getRequests();
        foreach ($requests as $r) {
            if ($r->getId() == $rid) {
                $r->setStatus(TeamRequestStatus::$REJECTED);
                $cache->delete(\md5($r->getUser()->getId() . '_teamid'));
                $cache->delete(\md5($r->getUser()->getId() . '_teamname'));
                break;
            }
        }
        
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Successfully rejected user!");
        
        return $this->redirect($this->generateUrl('ctf_team_admin'));
    }
    
    public function acceptAsAdminAction($tid, $rid, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (!$this->isTeamAdminTokenValid()) {
            return new Response('Bad Request!', 400);
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $teamid = $teamrepo->findAdminedByUserId($user->getId());
        
        if ($teamid === null || $teamid != $tid) {
            return new Response('Bad Request!', 400);
        }
        
        $team = $teamrepo->find($tid);
        
        $requests = $team->getRequests();
        $ctr = 0;
        foreach ($requests as $r) {
            if ($r->getStatus() == TeamRequestStatus::$ACCEPTED || $r->getStatus() == TeamRequestStatus::$ACCEPTEDANDADMIN) {
                ++$ctr;
            }
        }
        
        $max = (int) $this->container->getParameter('max_per_team');
        if ($ctr < $max) {
            foreach ($requests as $r) {
                if ($r->getId() == $rid) {
                    $existing = $teamrepo->findAcceptedRequestByUserId($r->getUser()->getId());
                    if ($existing == null || false == $existing) {
                        $r->setStatus(TeamRequestStatus::$ACCEPTEDANDADMIN);
                        $user = $r->getUser();
                        $user->setRoles(array('ROLE_TEAM_ADMIN'));

                        $this->get('fos_user.user_manager')->updateUser($user, false);
                    } else {
                        $this->get('session')->getFlashBag()->add('error', "User is already part of another team!");
                        return $this->redirect($this->generateUrl('ctf_team_admin'));
                    }
                    break;
                }
            }
            
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Successfully accepted user!");
        } else {
            $this->get('session')->getFlashBag()->add('error', "You have reached the maximum number of members for teams [" . $max . "]");
        }
        
        return $this->redirect($this->generateUrl('ctf_team_admin'));
    }
    
    public function makeAdminAction($tid, $rid, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (!$this->isTeamAdminTokenValid()) {
            return new Response('Bad Request!', 400);
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $teamid = $teamrepo->findAdminedByUserId($user->getId());
        
        if ($teamid === null || $teamid != $tid) {
            return new Response('Bad Request!', 400);
        }
        
        $team = $teamrepo->find($tid);
        
        $requests = $team->getRequests();
        
        foreach ($requests as $r) {
            if ($r->getId() == $rid) {
                $r->setStatus(TeamRequestStatus::$ACCEPTEDANDADMIN);
                $user = $r->getUser();
                $user->setRoles(array('ROLE_TEAM_ADMIN'));

                $this->get('fos_user.user_manager')->updateUser($user, false);
                break;
            }
        }

        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Successfully made admin!");
        
        return $this->redirect($this->generateUrl('ctf_team_admin'));
    }
    
    public function updateStatusAction($status, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            if (null !== $status && !empty($status)) {
                $user = $this->get('security.context')->getToken()->getUser();
                $em = $this->getDoctrine()->getEntityManager();
                $teamrepo = $em->getRepository('CTFTeamBundle:Team');
                $teamid = $teamrepo->findAdminedByUserId($user->getId());
                $team = $teamrepo->find($teamid);
                
                $team->setStatus($status);
                $em->flush();
                
                $data = array(
                    'result' => 'success',
                    'message' => 'Successfully set status!',
                    'status' => $status
                );
                
                return new Response(\json_encode($data));
            }
            
            $data = array(
                'result' => 'error',
                'message' => 'Could not set status at this time!'
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request!', 400);
    }
}
