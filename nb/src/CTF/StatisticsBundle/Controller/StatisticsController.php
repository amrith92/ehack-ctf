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
        $content = $this->renderView('CTFStatisticsBundle:Statistics:index.html.twig');
        $response->setEtag(\md5($content));
        $response->setPublic();
        
        if ($response->isNotModified($request)) {
            return $response;
        } else {
            $response->setContent($content);
            return $response;
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
    
    public function topTenOrganizationsAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $data = array(
                'backgroundColor' => 'transparent',
                'creditText' => '',
                'theme' => 'theme2',
                'title' => array(
                    'text' => 'Top Ten Organizations',
                    'fontColor' => '#fff'
                ),
                'legend' => array(
                    'fontColor' => '#fcfcfc'
                ),
                'data' => array(
                    array(
                        'type' => 'pie',
                        'showInLegend' => true,
                        'indexLabelFontColor' => '#e6e6e6',
                        'indexLabelFontSize' => 8,
                        'dataPoints' => $em->getRepository('CTFUserBundle:User')->getTopOrganizations(10)
                    )
                )
            );

            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function bottomTenOrganizationsAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $data = array(
                'theme' => 'theme2',
                'title' => array(
                    'text' => 'Bottom Ten Organizations'
                ),
                'legend' => array(
                    'fontColor' => '#fcfcfc'
                ),
                'data' => array(
                    array(
                        'type' => 'pie',
                        'showInLegend' => false,
                        'indexLabelFontColor' => '#e6e6e6',
                        'indexLabelFontSize' => 8,
                        'dataPoints' => $em->getRepository('CTFUserBundle:User')->getBottomOrganizations(10)
                    )
                )
            );

            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function topTwentyPlayersAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $players = $em->getRepository('CTFQuestBundle:UserQuest')->getTopTwenty();
        
        $content = $this->renderView('CTFStatisticsBundle:Statistics:display-players.html.twig', array(
            'players' => $players
        ));
        
        $response = new Response($content);
        $response->setSharedMaxAge(600);
        
        return $response;
    }
    
    public function bottomTwentyPlayersAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $players = $em->getRepository('CTFQuestBundle:UserQuest')->getBottomTwenty();
        
        $content = $this->renderView('CTFStatisticsBundle:Statistics:display-players.html.twig', array(
            'players' => $players
        ));
        
        $response = new Response($content);
        $response->setSharedMaxAge(600);
        
        return $response;
    }
    
    public function topTenTeamsAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $teams = $em->getRepository('CTFTeamBundle:Team')->findTopN(10);
        
        $content = $this->renderView('CTFStatisticsBundle:Statistics:display-teams.html.twig', array(
            'teams' => $teams
        ));
        
        $response = new Response($content);
        $response->setSharedMaxAge(600);
        
        return $response;
    }
    
    public function bottomTenTeamsAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $teams = $em->getRepository('CTFTeamBundle:Team')->findBottomN(10);
        
        $content = $this->renderView('CTFStatisticsBundle:Statistics:display-teams.html.twig', array(
            'teams' => $teams
        ));
        
        $response = new Response($content);
        $response->setSharedMaxAge(600);
        
        return $response;
    }
}
