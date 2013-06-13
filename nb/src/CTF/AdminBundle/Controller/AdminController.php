<?php

namespace CTF\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use \CTF\AdminBundle\Form\GlobalStateType;
use \CTF\AdminBundle\Form\AnnouncementType;

class AdminController extends Controller
{
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
                $em->persist($settings);
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
    
    public function teamCountAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $repo = $this->getDoctrine()->getEntityManager()->getRepository('CTFTeamBundle:Team');
            
            return new Response($repo->countOfTeams());
        }
        
        return new Response('', 400);
    }
    
    public function gendersCountAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $repo = $this->getDoctrine()->getEntityManager()->getRepository('CTFUserBundle:User');
            $counts = $repo->countsInGenders();
            
            $p = null;
            
            foreach  ($counts as $v) {
                $p[] = array(
                    'y' => (float)$v['1'],
                    'indexLabel' => \ucfirst($v['gender'])
                );
            }
            
            // Canvasjs JSON
            $data = array(
                'backgroundColor' => 'transparent',
                'creditText' => '',
                'animationEnabled' => true,
                'title' => array(
                    'text' => 'CTF Gender Ratio',
                    'fontColor' => '#fff'
                ),
                'data' => array(
                    array(
                        'type' => 'doughnut',
                        'indexLabelFontColor' => '#e6e6e6',
                        'dataPoints' => $p
                    )
                )
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('', 400);
    }
    
    public function chatAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        return $this->render('CTFGlobalChatBundle:GChat:index.html.twig');
    }
}
