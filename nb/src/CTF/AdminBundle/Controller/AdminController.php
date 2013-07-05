<?php

namespace CTF\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use \CTF\AdminBundle\Form\GlobalStateType;
use \CTF\AdminBundle\Form\AnnouncementType;

class AdminController extends Controller {

    public function indexAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $announceform = $this->createForm(new AnnouncementType());
        
        return $this->render('CTFAdminBundle:Admin:index.html.twig', array(
            'announceform' => $announceform->createView()
        ));
    }
    
    public function settingsAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new GlobalStateType());
            $form->bind($request);
            
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $settings = $form->getData();
                $settings->setId(1);
                
                $em->merge($settings);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Settings Updated!");
                $this->redirect($this->generateUrl('ctf_admin_settings'));
            } else {
                $this->get('session')->getFlashBag()->add('error', "Settings could not be updated!");
                return $this->render('CTFAdminBundle:Admin:settings.html.twig', array(
                    'form' => $form->createView()
                ));
            }
        }
        
        $settings = $this->getDoctrine()->getEntityManager()->getRepository('CTFAdminBundle:GlobalState')->find(1);
        $form = $this->createForm(new GlobalStateType(), $settings);
        
        return $this->render('CTFAdminBundle:Admin:settings.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function announceAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $em = $this->getDoctrine()->getEntityManager();
            $announcements = $em->getRepository('CTFAdminBundle:Announcement')->findAll();
            
            return $this->render('CTFAdminBundle:Admin:announce-history.html.twig', array(
                'history' => $announcements
            ));
        } else if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $form = $this->createForm(new AnnouncementType());
            $form->bind($request);
            
            if ($form->isValid()) {
                $announcement = $form->getData();
                $announcement->setDelivered(false);
                $em = $this->getDoctrine()->getEntityManager();
                
                $em->persist($announcement);
                $em->flush();
                
                return new Response();
            }
            
            $response = new Response();
            $response->setStatusCode(400);
            
            return $response;
        } else if ($request->isMethod('POST')) {
            $form = $this->createForm(new AnnouncementType());
            $form->bind($request);
            
            if ($form->isValid()) {
                $announcement = $form->getData();
                $announcement->setDelivered(false);
                $em = $this->getDoctrine()->getEntityManager();
                
                $em->merge($announcement);
                $em->persist($announcement);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Announcement Broadcasted!");
                $this->redirect($this->generateUrl('ctf_admin_announce'));
            } else {
                $this->get('session')->getFlashBag()->add('error', "Announcement could not be broadcasted at the moment. Please try again.");
                return $this->render('CTFAdminBundle:Admin:announce.html.twig', array(
                    'form' => $form->createView()
                ));
            }
        }
        
        $form = $this->createForm(new AnnouncementType());
        
        return $this->render('CTFAdminBundle:Admin:announce.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function chatAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        return $this->render('CTFGlobalChatBundle:GChat:index.html.twig');
    }
    
    public function listTeamsAction($query, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $teamlist = $em->getRepository('CTFTeamBundle:Team')->findTeamsByPartialName($query);
            
            $names = null;
            foreach ($teamlist as $team) {
                $names["options"][] = $team->getName();
            }
            
            $response = new Response(\json_encode($names), 200, array(
                'Content-Type' => 'application/json'
            ));
            
            return $response;
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function banTeamAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $team = $em->getRepository('CTFTeamBundle:Team')->findOneBy(array(
                'name' => $name
            ));
            
            if (null !== $team) {
                $team->setActive(false);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function unbanTeamAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $team = $em->getRepository('CTFTeamBundle:Team')->findOneBy(array(
                'name' => $name
            ));
            
            if (null !== $team) {
                $team->setActive(true);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function listUsersAction($query, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $userlist = $em->getRepository('CTFUserBundle:User')->findUsersByPartialUsername($query);
            
            $names = null;
            foreach ($userlist as $user) {
                $names["options"][] = $user->getUsername();
            }
            
            $response = new Response(\json_encode($names), 200, array(
                'Content-Type' => 'application/json'
            ));
            
            return $response;
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function banUserAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('CTFUserBundle:User')->findOneBy(array(
                'username' => $name
            ));
            
            if (null !== $user) {
                $user->setLocked(true);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function unbanUserAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('CTFUserBundle:User')->findOneBy(array(
                'username' => $name
            ));
            
            if (null !== $user) {
                $user->setLocked(false);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
}
