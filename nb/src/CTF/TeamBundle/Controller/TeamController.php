<?php

namespace CTF\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \Symfony\Component\Form\FormError;
use \CTF\TeamBundle\Entity\TeamMemberRequest;
use CTF\TeamBundle\Util\TeamRequestStatus;

class TeamController extends Controller {
    
    public function selectAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm($this->get('ctf.form.select_team'));
        $this->get('session')->getFlashBag()->add('notice', "Looks like you aren't a part of a team yet! If you've already sent a request, wait for confirmation or try another team ;)");
        
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
            
            if ($form->isValid()) {
                $dto = $form->getData();
                
                if ('create' == $dto->getIsSelecting()) {
                    if ($form['team']->isValid()) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $repo = $em->getRepository('CTFTeamBundle:Team');
                        $team = $dto->getTeam();
                        
                        if (null !== $repo->findOneBy(array('name' => $team->getName()))) {
                            $form->get('team')->addError(new FormError("Team-Name already exists!"));
                            return $this->render('CTFTeamBundle:Team:select-team.html.twig', array(
                                'form' => $form->createView()
                            ));
                        }
                        
                        if (isset($form['team']['attachment'])) {
                            $file = $form['team']['attachment']->getData();
                            $dir = __DIR__.'/../../../../web/uploads/team';
                            $extension = $file->guessExtension();
                            if (!$extension) {
                                // extension cannot be guessed
                                $extension = 'bin';
                            }
                            $newFileName = sha1($file . rand(0, 199992993)) . '.' . $extension;
                            $file->move($dir, $newFileName);
                            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                            $team->setTeamPic($baseurl . '/uploads/team/' . $newFileName);
                        }
                        
                        $team->setScore(0);
                        $em->persist($team);
                        
                        $user = $this->get('security.context')->getToken()->getUser();
                        $teamRequest = new TeamMemberRequest();
                        $teamRequest->setCreatedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                        $teamRequest->setStatus(TeamRequestStatus::$ACCEPTEDANDADMIN);
                        $teamRequest->setUser($user);
                        $team->addRequest($teamRequest);
                        
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
                    $teamRequest = new TeamMemberRequest();
                    $teamRequest->setCreatedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                    $teamRequest->setStatus(TeamRequestStatus::$REQUESTED);
                    $teamRequest->setUser($user);
                    $team->addRequest($teamRequest);
                    
                    $em->flush();
                    
                    $this->get('session')->getFlashBag()->add('success', "You've successfully sent a request to the team [" . $team->getName() . "]. Please wait for the team's ADMIN to accept or reject your request. You can always send out requests to more teams ;)");

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
}
