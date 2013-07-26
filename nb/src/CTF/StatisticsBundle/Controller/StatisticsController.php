<?php

namespace CTF\StatisticsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    
    public function topOrganizationsAction($n, Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $data = array(
                'backgroundColor' => 'transparent',
                'creditText' => '',
                'theme' => 'theme2',
                'title' => array(
                    'text' => 'Top Organizations',
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
                        'indexLabelFontSize' => 12,
                        'dataPoints' => $em->getRepository('CTFUserBundle:User')->getTopOrganizations($n)
                    )
                )
            );

            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function bottomOrganizationsAction($n, Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $data = array(
                'backgroundColor' => 'transparent',
                'creditText' => '',
                'theme' => 'theme2',
                'title' => array(
                    'text' => 'Bottom Organizations',
                    'fontColor' => '#fff'
                ),
                'legend' => array(
                    'fontColor' => '#fcfcfc'
                ),
                'data' => array(
                    array(
                        'type' => 'pie',
                        'showInLegend' => false,
                        'indexLabelFontColor' => '#e6e6e6',
                        'indexLabelFontSize' => 12,
                        'dataPoints' => $em->getRepository('CTFUserBundle:User')->getBottomOrganizations($n)
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
    
    public function userStatisticsAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $settings = $em->getRepository('CTFAdminBundle:GlobalState')->find(1);
        
        if (false === $settings->isStatsEnabled()) {
            return $this->render('CTFStatisticsBundle:Statistics:user-stats-locked.html.twig');
        }
        
        return $this->render('CTFStatisticsBundle:Statistics:user-stats.html.twig');
    }
    
    public function publicStatisticsAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $settings = $em->getRepository('CTFAdminBundle:GlobalState')->find(1);
        
        if (false === $settings->isStatsEnabled()) {
            return $this->render('CTFStatisticsBundle:Statistics:esi-user-stats-locked.html.twig');
        }
        
        return $this->render('CTFStatisticsBundle:Statistics:esi-pub-stats.html.twig');
    }
    
    public function worldUsersAction(Request $request) {
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $cache = $this->get('ctf_cache');
            
            $users = $cache->get('ctf_users_world_cache');
            
            if (false === $users) {
                $em = $this->getDoctrine()->getEntityManager();
                $arr = $em->getRepository('CTFUserBundle:User')->worldUsers();
                
                foreach ($arr as $u) {
                    if (null != $u->getLocation()) {
                        $users[] = array(
                            'username' => $u->getUsername(),
                            'id' => $u->getId(),
                            'dp' => $u->getImageURL(),
                            'organization' => $u->getOrg()->getName(),
                            'location' => array(
                                'lat' => $u->getLocation()->getLatitude(),
                                'lng' => $u->getLocation()->getLongitude()
                            )
                        );
                    }
                }

                $cache->add('ctf_users_world_cache', $users, 300);
            }
            
            $data = array(
                'result' => 'success',
                'users' => $users
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
}
