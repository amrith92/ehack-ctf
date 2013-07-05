<?php

namespace CTF\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use CTF\QuestBundle\Form\StageType;

class StageController extends Controller {
    
    public function stageAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $list = $em->getRepository('CTFQuestBundle:Stage')->findAll();
        
        return $this->render('CTFAdminBundle:Stage:stage.html.twig', array(
            'list' => $list
        ));
    }
    
    public function stageListAction($q, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $em = $this->getDoctrine()->getEntityManager();
        
            $list = $em->getRepository('CTFQuestBundle:Stage')->findAll();

            if ($q == 0) {
                return $this->render('CTFAdminBundle:Stage:stage.list.html.twig', array(
                    'list' => $list
                ));
            } else {
                return $this->render('CTFAdminBundle:Question:question.list.html.twig', array(
                    'list' => $list
                ));
            }
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function stageFormAction($id, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if (-1 == $id) {
            if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
                $form = $this->createForm(new StageType());
                return $this->render('CTFAdminBundle:Stage:stage.form.html.twig', array(
                    'form' => $form->createView()
                ));
            } else if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
                $form = $this->createForm(new StageType());
                $form->bind($request);

                if ($form->isValid()) {
                    $data = $form->getData();
                    
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($data);
                    $em->flush();

                    return new Response('true');
                } else {
                    return new Response('false');
                }
            }
        } else {
            if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
                $repo = $this->getDoctrine()->getEntityManager()->getRepository('CTFQuestBundle:Stage');
                $form = $this->createForm(new StageType(), $repo->find($id));
                
                return $this->render('CTFAdminBundle:Stage:stage.form.html.twig', array(
                    'form' => $form->createView(),
                    'edit' => $id
                ));
            } else if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
                $em = $this->getDoctrine()->getEntityManager();
                $repo = $em->getRepository('CTFQuestBundle:Stage');
                $form = $this->createForm(new StageType(), $repo->find($id));
                
                $form->bind($request);
                
                if ($form->isValid()) {
                    $em->persist($form->getData());
                    $em->flush();
                    
                    return new Response('true');
                } else {
                    return new Response('false');
                }
            }
        }
        
        return new Response('Bad Request!', 400);
    }
}
