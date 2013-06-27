<?php

namespace CTF\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use \CTF\QuestBundle\Entity\Stage;
use \CTF\AdminBundle\Form\GlobalStateType;
use \CTF\AdminBundle\Form\AnnouncementType;
use \CTF\QuestBundle\Form\StageType;
use \CTF\QuestBundle\Form\QuestionType;

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
                $announcement->setDelivered(false);
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
                $announcement->setDelivered(false);
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
    
    public function chatAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        return $this->render('CTFGlobalChatBundle:GChat:index.html.twig');
    }
    
    public function stageAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $list = $em->getRepository('CTFQuestBundle:Stage')->findAll();
        
        return $this->render('CTFAdminBundle:Admin:stage.html.twig', array(
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
                return $this->render('CTFAdminBundle:Admin:stage.list.html.twig', array(
                    'list' => $list
                ));
            } else {
                return $this->render('CTFAdminBundle:Admin:question.list.html.twig', array(
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
                return $this->render('CTFAdminBundle:Admin:stage.form.html.twig', array(
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
                
                return $this->render('CTFAdminBundle:Admin:stage.form.html.twig', array(
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
    
    public function questionAction($id, $stage, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if (-1 == $id) {
            if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
                $form = $this->createForm(new QuestionType());
                
                return $this->render('CTFAdminBundle:Admin:question.form.html.twig', array(
                    'form' => $form->createView()
                ));
            } else if ($request->isMethod('POST')) {
                $form = $this->createForm(new QuestionType());
                
                $form->bind($request);
                
                if ($form->isValid()) {
                    // Get Question
                    $question = $form->getData();
                    
                    // Get stage
                    $stage = $form['stage']->getData()->getId();
                    $level = $question->getLevel();
                    
                    // Check for the attachment
                    if (null !== $form['attachment']->getData()) {
                        $file = $form->get('attachment')->getData();
                        $salt = $this->container->getParameter('secret');
                        $dir = __DIR__.'/../../../../web/uploads/questions/' . md5($salt . '/s' . $stage . $salt . '/l' . $level . $salt);
                        $extension = $file->guessExtension();
                        if (!$extension) {
                            // extension cannot be guessed
                            $extension = 'bin';
                        }
                        $newFileName = sha1($file . rand(0, 199992993)) . '.' . $extension;
                        
                        if(!\file_exists($dir)) {
                            $fs = new Filesystem();
                            
                            try {
                                $fs->mkdir($dir);
                            } catch (Exception $e) {
                                /*return new Response(\json_encode(array(
                                    'result' => 'error',
                                    'message' => 'Stage/Level already exists!'
                                )));*/
                            }
                        }
                        
                        $file->move($dir, $newFileName);
                        
                        // Unzip the file
                        $zip = new \ZipArchive();
                        $res = $zip->open($dir . DIRECTORY_SEPARATOR . $newFileName);
                        if ($res === TRUE) {
                            $zip->extractTo($dir);
                            $zip->close();
                        } else {
                            return new Response(\json_encode(array(
                                'result' => 'error',
                                'message' => 'Not a zip-file!'
                            )));
                        }
                    }
                    
                    $em = $this->getDoctrine()->getEntityManager();
                    
                    $stages = $em->getRepository('CTFQuestBundle:Stage')->find($stage);
                    $stages->addQuestion($question);
                    
                    $em->flush();
                    
                    $this->get('session')->getFlashBag()->add('success', "Successfully saved question!");
                    return $this->redirect($this->generateUrl('ctf_admin_stage'));
                } else {
                    return new Response(\json_encode(array(
                        'result' => 'error',
                        'message' => 'Some fields are invalid. Unable to save!'
                    )));
                }
            }
        } else {
            if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
                $em = $this->getDoctrine()->getEntityManager();
                $repo = $em->getRepository('CTFQuestBundle:Question');
                $form = $this->createForm(new QuestionType(), $repo->find($id));
                $srepo = $em->getRepository('CTFQuestBundle:Stage');
                $form['stage']->setData($srepo->find($stage));
                
                return $this->render('CTFAdminBundle:Admin:question.edit.form.html.twig', array(
                    'form' => $form->createView(),
                    'qid' => $id,
                    'sid' => $stage
                ));
            } else if (-1 != $stage && $request->isMethod('POST')) {
                $form = $this->createForm(new QuestionType());
                $form->bind($request);
                
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $question = $em->getRepository('CTFQuestBundle:Question')->find($id);
                    $stage = $em->getRepository('CTFQuestBundle:Stage')->find($stage);
                    
                    // Attachment
                    if (null !== $form['attachment']->getData()) {
                        $file = $form->get('attachment')->getData();
                        $dir = __DIR__.'/../../../../web/uploads/questions' . '/s' . $stage->getId() . '/l' . $question->getLevel();
                        $extension = $file->guessExtension();
                        if (!$extension) {
                            // extension cannot be guessed
                            $extension = 'bin';
                        }
                        $newFileName = sha1($file . rand(0, 199992993)) . '.' . $extension;
                        
                        if(!\file_exists($dir)) {
                            $fs = new Filesystem();
                            
                            try {
                                $fs->mkdir($dir);
                            } catch (Exception $e) {
                                /*return new Response(\json_encode(array(
                                    'result' => 'error',
                                    'message' => 'Stage/Level already exists!'
                                )));*/
                            }
                        }
                        
                        $file->move($dir, $newFileName);
                        
                        // Unzip the file
                        $zip = new \ZipArchive();
                        $res = $zip->open($dir . DIRECTORY_SEPARATOR . $newFileName);
                        if ($res === TRUE) {
                            $zip->extractTo($dir);
                            $zip->close();
                        } else {
                            return new Response(\json_encode(array(
                                'result' => 'error',
                                'message' => 'Not a zip-file!'
                            )));
                        }
                    }
                    
                    $question->setContent($form['content']->getData());
                    $question->setTitle($form['title']->getData());
                    $question->setLevel($form['level']->getData());
                    $question->setAnswerTemplate($form['answerTemplate']->getData());
                    $question->setHints($form['hints']->getData());

                    if (true === $stage->hasQuestion($question)) {
                        if ($form['stage']->getData()->getId() != $stage->getId()) {
                            // Remove question from stage collection
                            $stage->getQuestions()->removeElement($question);
                            $em->flush();
                            
                            $newstage = $em->getRepository('CTFQuestBundle:Stage')->find($form['stage']->getData()->getId());
                            $newstage->addQuestion($question);
                            $em->merge($newstage);
                        }
                    } else {
                        $stage->addQuestion($question);
                        $em->merge($stage);
                    }
                    
                    $em->flush();
                    
                    $this->get('session')->getFlashBag()->add('success', "Saved Question!");
                    return $this->redirect($this->generateUrl('ctf_admin_stage'));
                } else {
                    $this->get('session')->getFlashBag()->add('error', "Could not save question!");
                    return $this->redirect($this->generateUrl('ctf_admin_stage'));
                }
            }
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function listTeamsAction($query, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $teamlist = $em->getRepository('CTFTeamBundle:Team')->findTeamsByPartialName($query);
            
            $names = null;
            foreach ($teamlist as $team) {
                $names["options"][] = $team->getName();
            }
            
            $response = new Response(\json_encode($names), 200, array(
                'Content-Type' => 'application/json'
            ));
            
            return $response;
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function banTeamAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $team = $em->getRepository('CTFTeamBundle:Team')->findOneBy(array(
                'name' => $name
            ));
            
            if (null !== $team) {
                $team->setActive(false);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function unbanTeamAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $team = $em->getRepository('CTFTeamBundle:Team')->findOneBy(array(
                'name' => $name
            ));
            
            if (null !== $team) {
                $team->setActive(true);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function listUsersAction($query, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $userlist = $em->getRepository('CTFUserBundle:User')->findUsersByPartialUsername($query);
            
            $names = null;
            foreach ($userlist as $user) {
                $names["options"][] = $user->getUsername();
            }
            
            $response = new Response(\json_encode($names), 200, array(
                'Content-Type' => 'application/json'
            ));
            
            return $response;
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function banUserAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('CTFUserBundle:User')->findOneBy(array(
                'username' => $name
            ));
            
            if (null !== $user) {
                $user->setLocked(true);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
    
    public function unbanUserAction($name, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('CTFUserBundle:User')->findOneBy(array(
                'username' => $name
            ));
            
            if (null !== $user) {
                $user->setLocked(false);
                $em->flush();
                
                $data = array(
                    'result' => 'success'
                );
            } else {
                $data = array(
                    'result' => 'failure'
                );
            }
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request.', 400);
    }
}
