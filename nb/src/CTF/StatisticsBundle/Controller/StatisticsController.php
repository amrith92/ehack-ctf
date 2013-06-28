<?php

namespace CTF\StatisticsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    public function indexAction(Request $request)
    {
        $response = new Response();
        $response->setEtag(\md5("index.page.ctf"));
        $response->setPublic();
        
        if ($response->isNotModified($request)) {
            return $response;
        } else {
            return $this->render('CTFStatisticsBundle:Statistics:index.html.twig', null, $response);
        }
    }
    
    public function gendersCountAction(Request $request) {
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
        
        return new Response('Bad Request.', 400);
    }
    
    public function teamCountAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $repo = $this->getDoctrine()->getEntityManager()->getRepository('CTFTeamBundle:Team');
            
            return new Response($repo->countOfTeams());
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function totalPlayersAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $repo = $this->getDoctrine()->getEntityManager()->getRepository('CTFUserBundle:User');
            
            return new Response($repo->count());
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function nearbyPubAction($bounds, Request $request) {
        if ($request->isXmlHttpRequest()) {
            $users = $this->getDoctrine()->getEntityManager()->getRepository('CTFUserBundle:User')->findUsersWithinBounds(\json_decode($bounds));
            
            if (null != $users) {
                foreach ($users as $m => $n) {
                    foreach ($n as $k => $v) {
                        if ('location' == $k) {
                            $tmp = \str_replace('POINT(', '', $v);
                            $tmp = \substr($tmp, 0, \strlen($tmp) - 1);
                            $ret = preg_split('/ /', $tmp);
                            $latlng = array(
                                'lat' => $ret[0],
                                'lng' => $ret[1]
                            );
                            $users[$m][$k] = $latlng;
                            break;
                        }
                    }
                }
                
                $data = array(
                    'status' => 'success',
                    'users' => $users
                );
            } else {
                $data = array(
                    'status' => 'error'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
}
