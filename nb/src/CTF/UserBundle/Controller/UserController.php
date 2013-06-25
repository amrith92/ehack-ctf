<?php

namespace CTF\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller {

    public function editProfileAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm($this->get('ctf.form.profile_edit'), $user, array(
            'em' => $this->getDoctrine()->getManager()
        ));
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if (!isset($form['state'])) {
                $this->get('session')->getFlashBag()->add('error', "Please select a country AND state!");
                return $this->render('CTFUserBundle:User:editprofile.html.twig', array(
                    'form' => $form->createView(),
                    'profilePic' => $user->getImageURL()
                ));
            }
            
            if ($form->isValid()) {
                //$em = $this->getDoctrine()->getEntityManager();
                $data = $form->getData();
                if ($data->getCountry() == null) {
                    $this->get('session')->getFlashBag()->add('error', "Please select a valid country!");
                    return $this->render('CTFUserBundle:User:editprofile.html.twig', array(
                        'form' => $form->createView(),
                        'profilePic' => $user->getImageURL()
                    ));
                }
                if (isset($form['password'])) {
                    $passwd = $form['password']->getData();
                }
                $userManager = $this->get('fos_user.user_manager');
                $user->setFname($data->getFname());
                $user->setLname($data->getLname());
                $user->setPhone($data->getPhone());
                if (isset($passwd)) {
                    $user->setPlainPassword($passwd);
                }
                $user->setCountry($data->getCountry());
                $user->setState($data->getState());
                $user->setDob($data->getDob());
                $user->setAboutMe($data->getAboutMe());
                $user->setGender($data->getGender());
                $user->setCity($data->getCity());
                $user->setWebsite($data->getWebsite());
                $userManager->updateUser($user, true);
                
                $this->get('session')->getFlashBag()->add('success', "All changes saved!");
                return $this->redirect($this->generateUrl('ctf_user_edit_profile'));
            } else {
                $this->get('session')->getFlashBag()->add('error', "You have entered invalid data in the form!");
            }
        } else {
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
                } else if ('facebook' === $user->getLoginMode()) {
                    if (true === $this->get('ctf_user_util')->populateWithFacebook($user)) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->merge($user);
                        $em->persist($user);
                        $em->flush();
                    }
                } else if ('twitter' == $user->getLoginMode()) {
                    if (true === $this->get('ctf_user_util')->populateWithTwitter($user)) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->merge($user);
                        $em->persist($user);
                        $em->flush();
                    }
                }
            }

            $form->setData($user);
        }
        
        return $this->render('CTFUserBundle:User:editprofile.html.twig', array(
            'form' => $form->createView(),
            'profilePic' => $user->getImageURL()
        ));
    }
    
    public function grabFormAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $form = $this->createForm($this->get('ctf.form.profile_edit'), null, array(
                'em' => $this->getDoctrine()->getManager()
            ));
            $form->bind($request);
            
            return $this->render('CTFUserBundle:User:editprofileform.html.twig', array(
                'form' => $form->createView()
            ));
        }
        
        return new Response('Bad Request!', 400);
    }

}
