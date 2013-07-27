<?php

namespace CTF\RegistrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use CTF\UserBundle\Form\EssentialUserType;
use CTF\ReferralBundle\Form\SmsType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Buzz\Browser;
use Buzz\Client\Curl;

class RegistrationController extends Controller {

    public function indexAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        
        if ('google' === $user->getLoginMode()) {
            if (true === $this->get('ctf_user_util')->populateWithGooglePlus($user)) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->merge($user);
                $em->persist($user);
                $em->flush();
            }
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
        
        $essential = $this->createForm(new EssentialUserType(), $user, array(
            'em' => $em
                ));

        return $this->render('CTFRegistrationBundle:Registration:index.html.twig', array(
                    'essential' => $essential->createView()
                ));
    }

    public function basicAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $form = $this->createForm(new EssentialUserType(), null, array(
                'em' => $em
                    ));

            $form->bind($request);

            if ($form->isValid()) {
                
                if (!$form['coe']->getData()) {
                    $data = array(
                        'result' => 'error',
                        'message' => 'You HAVE to agree with the code of ethics and terms to continue.'
                    );

                    return new Response(\json_encode($data));
                }
                
                $user = $form->getData();
                $_user = $this->get('security.context')->getToken()->getUser();
                $_user->setEmail($user->getEmail());
                $_user->setFname($user->getFname());
                $_user->setLname($user->getLname());
                $_user->setPhone($user->getPhone());
                $_user->setOrg($user->getOrg());
                $_user->setLocation($user->getLocation());
                $this->get('fos_user.user_manager')->updateUser($_user, false);
                $em->merge($_user);
                $em->flush();
                
                $sms = \substr(\md5($_user->getId() . time()), 0, 6);
                
                $sms_form = $this->createForm(new SmsType());
                
                $data = array(
                    'result' => 'success',
                    'message' => "You've been sent an SMS containing your One-Time Password to the provided mobile number: " . $user->getPhone(),
                    'sms' => $sms,
                    'smsform' => $this->renderView('CTFRegistrationBundle:Registration:sms.form.html.twig', array('form' => $sms_form->createView()))
                );
                
                $browser = new Browser(new Curl());
                $smsmsg = 'Hi ' . $user->getFname() . '! Your OTP for the registration is ' . $sms . '. Thank you for your interest :)';
                $url = 'http://smstp.itsolusenz.com/sendsms.jsp?user=ehacksms&password=demo1234&mobiles=' . $user->getPhone() . '&sms=' . \urlencode($smsmsg) . '&unicode=0&senderid=EHACKO&version=3';
                
                $browser->get($url);
                
                $response = new Response(\json_encode($data));
                //$response->headers->setCookie(new Cookie('sms', $sms, 0, '/', null, false, false));
                $cache = $this->get('ctf_cache');
                $cache->store(\md5($user->getId() . '_sms_verify'), $sms);

                return $response;
            }

            $data = array(
                'result' => 'error',
                'message' => 'Something is not quite right with the data you have submitted!'
            );

            return new Response(\json_encode($data));
        }

        return new Response('Bad Request!', 400);
    }
    
    public function sendEmailAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $cache = $this->get('ctf_cache');
            $sms_verify_against = $cache->get(\md5($user->getId() . '_sms_verify'));
            $message = \Swift_Message::newInstance()
                ->setSubject('OTP :: CTF Ehack <ctf.ehack.in>')
                ->setFrom('noreply@ehack.in')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        ':Email:otp.html.twig',
                        array(
                            'name' => $user->getFname(),
                            'otp' => $sms_verify_against
                        )
                    )
                )
            ;
            
            $this->get('mailer')->send($message);
            
            return new Response();
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function smsAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $form = $this->createForm(new SmsType());
            
            $form->bind($request);
            
            if ($form->isValid()) {
                $sms = $form->get('sms')->getData();
                $cache = $this->get('ctf_cache');
                $user = $this->get('security.context')->getToken()->getUser();
                $sms_verify_against = $cache->get(\md5($user->getId() . '_sms_verify'));
                
                if ($sms == $sms_verify_against) {
                    $user->setVerified(true);
                    $this->get('fos_user.user_manager')->updateUser($user);
                    $this->get('session')->set('__MYREF', $this->get('ctf_referral.cryptor')->encrypt($user->getId()));
                    
                    $data = array(
                        'result' => 'success',
                        'message' => 'You have successfully activated your account!',
                        'share' => true,
                        'sharepage' => $this->renderView('CTFRegistrationBundle:Registration:share.html.twig')
                    );
                    
                    return new Response(\json_encode($data));
                }
            }
            
            $data = array(
                'result' => 'error',
                'message' => 'Your OTP does not match!'
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function wrapUpAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $ref = (null != $this->get('session')->get('registration_ref')) ? $this->get('session')->get('registration_ref') : -1;
        
        return $this->redirect($this->generateUrl('ctf_referral_register', array(
            'ref' => $ref
        )));
    }

}
