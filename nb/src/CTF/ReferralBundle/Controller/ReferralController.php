<?php

namespace CTF\ReferralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Buzz\Browser;
use Buzz\Client\Curl;

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
    
    public function shortenReferralLinkAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $cache = $this->get('ctf_cache');
        $user = $this->get('security.context')->getToken()->getUser();
        
        if ($cache->has(\md5( $user->getId() . '_refshorturl' ))) {
            $shortUrl = $cache->get(\md5( $user->getId() . '_refshorturl' ));
            
            return new Response($shortUrl);
        } else {
            $apikey = $this->container->getParameter('google_api_key');

            $browser = new Browser(new Curl());
            $url = "https://www.googleapis.com/urlshortener/v1/url?fields=id&key=" . $apikey;
            $headers = array(
                'Content-Type' => 'application/json',
                'X-JavaScript-User-Agent' => 'Buzz via Curl'
            );
            $data = array(
                "longUrl" => $this->generateUrl('ctf_referral_refer', array(
                    'ref' => $this->get('session')->get('__MYREF')
                ), true)
            );
            $content = \json_encode($data);

            $response = $browser->post($url, $headers, $content)->getContent();

            $json = \json_decode($response);

            if (null != $json) {
                if (\property_exists($json, 'error')) {
                    // Error
                    return new Response('Something went wrong!');
                } else {
                    $shortUrl = $json->id;
                    $cache->store(\md5( $user->getId() . '_refshorturl' ), $shortUrl, 31536000);
                    
                    return new Response($shortUrl);
                }
            }
        }
        
        return new Response('Bad Request.', 400);
    }

}
