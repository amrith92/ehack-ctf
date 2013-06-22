<?php

namespace CTF\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \Symfony\Component\Form\FormError;
use \CTF\TeamBundle\Entity\TeamMemberRequest;
use CTF\TeamBundle\Util\TeamRequestStatus;

class TeamController extends Controller {

    public function selectAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_TEAM_ADMIN')) {
            return $this->redirect($this->generateUrl('ctf_team_admin'));
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm($this->get('ctf.form.select_team'));
        $requests = $this->getDoctrine()->getEntityManager()->getRepository('CTFTeamBundle:Team')->findRequestsByUserId($user->getId());

        return $this->render('CTFTeamBundle:Team:select-team.html.twig', array(
                    'form' => $form->createView(),
                    'requests' => $requests
                ));
    }

    public function selectAjaxAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm($this->get('ctf.form.select_team'));

        if (true === $request->isMethod('POST') && $request->isXmlHttpRequest()) {
            // process select-team form
            $form->bind($request);

            $select = '';
            if ($form->getData()->getIsSelecting() == 'select') {
                $select = 'selectmap';
            }

            return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                        'form' => $form->createView(),
                        'selectmap' => $select
                    ));
        }
    }

    public function selectTeamAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if (true === $request->isMethod('POST')) {
            $form = $this->createForm($this->get('ctf.form.select_team'));
            $form->bind($request);
            
            $user = $this->get('security.context')->getToken()->getUser();
            
            if ($user->hasRole('ROLE_TEAM_ADMIN')) {
                $this->get('session')->getFlashBag()->add('notice', "You are ALREADY a team admin. You are not allowed to select/create other teams!");
                return $this->redirect($this->generateUrl('ctf_team_select'));
            }

            if ($form->isValid()) {
                $dto = $form->getData();

                if ('create' == $dto->getIsSelecting()) {
                    if ($form['team']->isValid()) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $repo = $em->getRepository('CTFTeamBundle:Team');
                        
                        $requests = $repo->findRequestsByUserId($user->getId());
                        foreach ($requests as $r) {
                            if ($r->getStatus() == TeamRequestStatus::$ACCEPTED || $r->getStatus() == TeamRequestStatus::$ACCEPTEDANDADMIN) {
                                $this->get('session')->getFlashBag()->add('error', "You are ALREADY a part of a team! You cannot create a team once you are accepted in a team.");
                                return $this->redirect($this->generateUrl('ctf_team_select'));
                            }
                        }
                        
                        $team = $dto->getTeam();

                        if (null !== $repo->findOneBy(array('name' => $team->getName()))) {
                            $form->get('team')->addError(new FormError("Team-Name already exists!"));
                            return $this->render('CTFTeamBundle:Team:select-team.html.twig', array(
                                        'form' => $form->createView()
                                    ));
                        }

                        if (isset($form['team']['attachment'])) {
                            $file = $form['team']['attachment']->getData();
                            $dir = __DIR__ . '/../../../../web/uploads/team';
                            $extension = $file->guessExtension();
                            if (!$extension) {
                                // extension cannot be guessed
                                $extension = 'bin';
                            }
                            
                            $mime_types = array(
                                "gif" => "image/gif"
                                ,"png" => "image/png"
                                ,"jpeg" => "image/jpg"
                                ,"jpg" => "image/jpg"
                                ,"bmp" => "images/bmp"
                            );
                            
                            if (!\in_array($extension, $mime_types)) {
                                $form->addError(new FormError("Invalid file-type! Please upload ONLY image files."));
                                $this->get('session')->getFlashBag()->add('error', "Please upload image-files ONLY.");
                                return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                                    'form' => $form->createView()
                                ));
                            }
                            
                            if (512000 < $file->getClientSize()) {
                                $form->addError(new FormError("Maximum upload size is 500KB."));
                                $this->get('session')->getFlashBag()->add('error', "Maximum upload size is 500KB.");
                                return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                                    'form' => $form->createView()
                                ));
                            }
                            
                            $newFileName = sha1($file . rand(0, 199992993)) . '.' . $extension;
                            $file->move($dir, $newFileName);
                            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                            $team->setTeamPic($baseurl . '/uploads/team/' . $newFileName);
                        }

                        $team->setScore(0);
                        $em->persist($team);

                        $user->addRole('ROLE_TEAM_ADMIN');
                        $teamRequest = new TeamMemberRequest();
                        $teamRequest->setCreatedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                        $teamRequest->setStatus(TeamRequestStatus::$ACCEPTEDANDADMIN);
                        $teamRequest->setUser($user);
                        $team->addRequest($teamRequest);
                        $this->get('fos_user.user_manager')->updateUser($user, false);

                        $em->flush();
                        $this->get('session')->getFlashBag()->add('success', "You've successfully created a team. You are now its ADMIN. Go forth, set out on your journey in this exciting competition.");

                        $this->redirect($this->generateUrl('ctf_quest_homepage'));
                    } else {
                        $this->get('session')->getFlashBag()->add('error', "Something went wrong whilst creating a team for you. Please try again.");
                        return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                                    'form' => $form->createView()
                                ));
                    }
                } else {
                    // Selected team
                    $em = $this->getDoctrine()->getEntityManager();
                    $team = $dto->getTeam();
                    $em->merge($team);

                    $user = $this->get('security.context')->getToken()->getUser();

                    // Check to see if user has already sent a request
                    $alreadyRequested = false;
                    $requests = $team->getRequests();
                    foreach ($requests as $r) {
                        if ($r->getUser()->getId() == $user->getId()) {
                            $alreadyRequested = true;
                            break;
                        }
                    }

                    if (false == $alreadyRequested) {
                        $teamRequest = new TeamMemberRequest();
                        $teamRequest->setCreatedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                        $teamRequest->setStatus(TeamRequestStatus::$REQUESTED);
                        $teamRequest->setUser($user);
                        $team->addRequest($teamRequest);

                        $em->flush();

                        $this->get('session')->getFlashBag()->add('success', "You've successfully sent a request to the team [" . $team->getName() . "]. Please wait for the team's ADMIN to accept or reject your request. You can always send out requests to more teams ;)");
                    } else {
                        $this->get('session')->getFlashBag()->add('error', "You've already made a request to this team :P");
                    }

                    return $this->redirect($this->generateUrl('ctf_team_select'));
                }
            } else {
                return $this->render('CTFTeamBundle:Team:select-team.form.html.twig', array(
                            'form' => $form->createView()
                        ));
            }
        }

        $this->get('session')->getFlashBag()->add('notice', "Whoopsy-Daisy! How'd you get to that page? :P");
        return $this->redirect($this->generateUrl('ctf_team_select'));
    }
    
    public function teamAdminAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        // Extra-security so that only admins can access the team-actions
        $user = $this->get('security.context')->getToken()->getUser();
        $salt = $this->container->getParameter('secret');
        $this->get('session')->set('team_admin_auth', md5($salt . $user->getId() . $salt));
        
        // Figure out which team to adminify
        $em = $this->getDoctrine()->getEntityManager();
        $teamrepo = $em->getRepository('CTFTeamBundle:Team');
        $teamid = $teamrepo->findAdminedByUserId($user->getId());
        $team = $teamrepo->find($teamid);
        
        return $this->render('CTFTeamBundle:Team:teamadmin.html.twig', array(
            'team' => $team
        ));
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
                    $r->setStatus(TeamRequestStatus::$ACCEPTED);
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
        
        $requests = $team->getRequests();
        foreach ($requests as $r) {
            if ($r->getId() == $rid) {
                $r->setStatus(TeamRequestStatus::$REJECTED);
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
                    $r->setStatus(TeamRequestStatus::$ACCEPTEDANDADMIN);
                    $user = $r->getUser();
                    $user->setRoles(array('ROLE_TEAM_ADMIN'));

                    $this->get('fos_user.user_manager')->updateUser($user, false);
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
