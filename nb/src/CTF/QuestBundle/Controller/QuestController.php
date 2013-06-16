<?php

namespace CTF\QuestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \CTF\QuestBundle\Entity\UserQuest;
use \CTF\QuestBundle\Entity\QuestHistoryItem;
use \CTF\QuestBundle\Util\QuestUtil;
use \CTF\QuestBundle\Form\AnswerType;

class QuestController extends Controller
{
    public function dashboardAction($ref, Request $request)
    {
        if (-1 != $ref) {
            $this->get('session')->set('registration_ref', $ref);
        }
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (false == $this->get('ctf_user_util')->hasFullyRegistered($user)) {
            $this->get('session')->getFlashBag()->add('notice', "You've logged in successfully, but your profile isn't quite complete yet! Take a moment to fill in necessary details.");
            return $this->redirect($this->generateUrl('ctf_user_edit_profile'));
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $stages = $em->getRepository('CTFQuestBundle:Stage')->findAll();
        $team = $em->getRepository('CTFTeamBundle:Team')->findAcceptedRequestByUserId($user->getId());
        
        $salt = $this->container->getParameter('secret');
        
        return $this->render('CTFQuestBundle:Quest:dashboard.html.twig', array(
            'user' => $user,
            'stages' => $stages,
            'team' => \md5($team . $salt)
        ));
    }
    
    public function continueQuestAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();
            
            $quest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);
            
            if (null === $quest) {
                // User hasn't endeavoured on the quest yet
                $quest = new UserQuest();
                $stage = $em->getRepository('CTFQuestBundle:Stage')->findFirst();
                $questions = $stage->getQuestions();
                
                foreach ($questions as $v) {
                    if ($v->getLevel() == 1) {
                        $level = $v;
                        break;
                    } else {
                        $level = null;
                    }
                }
                
                $quest->setQuestStage($stage);
                $quest->setCurrentStage($stage);
                $quest->setCurrentLevel($level);
                $quest->setQuestLevel($level);
                $quest->setScore(0);
                $quest->setUser($user);
                
                $item = new QuestHistoryItem();
                $item->setAttemptedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                $item->setFirstAttemptTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                $item->setQuestion($level);
                $item->setHintUsed(false);
                $item->setStatus(QuestUtil::$ATTEMPTING);
                $quest->addHistoryItem($item);
                
                $em->persist($quest);
                $em->flush();
                
                $question = $level;
            } else {
                $quest->setCurrentStage($quest->getQuestStage());
                $quest->setCurrentLevel($quest->getQuestLevel());
                $em->merge($quest);
                $em->flush();
                
                $question = $quest->getQuestLevel();
            }
            
            // TODO ///
            // Check to see whether any attachments are associated with this question
            ///////////
            $salt = $this->container->getParameter('secret');
            $attachment = null;
            $dir = __DIR__.'/../../../../web/uploads/questions/' . md5($salt . '/s' . $quest->getCurrentStage()->getId() . $salt . '/l' . $question->getLevel() . $salt);
            if(\file_exists($dir)) {
                $attachment = true;
            }
            
            $answer = $this->createForm(new AnswerType());

            return $this->render('CTFQuestBundle:Quest:question.display.html.twig', array(
                'answer' => $answer->createView(),
                'question' => $question,
                'attachment' => ($attachment) ? $dir : null
            ));
        }
        
        return new Response("Bad Request!", 400);
    }
    
    public function fetchQuestionAction($qid, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $this->get('security.context')->getToken()->getUser();
            $question = $em->getRepository('CTFQuestBundle:Question')->find($qid);
            if (null != $question) {
                $stage = $em->getRepository('CTFQuestBundle:Stage')->findByQuestion($qid);

                $userquest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);

                if ($stage->getId() <= $userquest->getQuestStage() && $question->getLevel() <= $userquest->getQuestLevel()->getLevel()) {
                    // Check if there's some history associated with this question,stage,level and user
                    $history = $userquest->getHistory();
                    $ffound = false;
                    foreach ($history as $h) {
                        if ($h->getQuestion()->getId() == $qid) {
                            $ffound = true;
                            break;
                        }
                    }
                    
                    if (false === $ffound) {
                        $item = new QuestHistoryItem();
                        $item->setAttemptedTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                        $item->setFirstAttemptTimestamp(new \DateTime(date('Y-m-d H:i:s')));
                        $item->setQuestion($question);
                        $item->setHintUsed(false);
                        $item->setStatus(QuestUtil::$ATTEMPTING);
                        $userquest->addHistoryItem($item);
                    }
                    
                    // Set current stage & level
                    $userquest->setCurrentStage($stage);
                    $userquest->setCurrentLevel($question);
                    $em->merge($userquest);
                    $em->flush();
                    
                    // TODO ///
                    // Check to see whether any attachments are associated with this question
                    ///////////
                    $salt = $this->container->getParameter('secret');
                    $attachment = null;
                    $dir = __DIR__.'/../../../../web/uploads/questions/' . md5($salt . '/s' . $stage->getId() . $salt . '/l' . $question->getLevel() . $salt);
                    if(\file_exists($dir)) {
                        $attachment = true;
                    }
                    
                    // Return question
                    $answer = $this->createForm(new AnswerType());
                    $message = $this->render('CTFQuestBundle:Quest:question.display.html.twig', array(
                        'question' => $question,
                        'answer' => $answer->createView(),
                        'attachment' => ($attachment) ? $dir : null
                    ))->getContent();
                    
                    $data = array(
                        'result' => 'success',
                        'message' => $message
                    );
                    
                    return new Response(\json_encode($data));
                } else {
                    // Not allowed to view the question
                    $message = $this->render('CTFQuestBundle:Quest:question.invalid.html.twig')->getContent();
                    
                    $data = array(
                        'result' => 'error',
                        'message' => $message
                    );
                    return new Response(\json_encode($data));
                }
            } else {
                $message = $this->render('CTFQuestBundle:Quest:question.invalid.html.twig')->getContent();
                    
                $data = array(
                    'result' => 'error',
                    'message' => $message
                );
                return new Response(\json_encode($data));
            }
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function showCurrentAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();
            $userquest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);
            
            $data = array(
                'stage' => $userquest->getCurrentStage()->getId(),
                'level' => $userquest->getCurrentLevel()->getLevel(),
                'title' => $userquest->getCurrentLevel()->getTitle(),
                'score' => $userquest->getScore()
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function grabHintAction($id, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();
            $userquest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);
            
            $history = $userquest->getHistory();
            
            foreach ($history as $item) {
                if ($item->getQuestion()->getId() == $id) {
                    $it = $item;
                    break;
                } else {
                    $it = null;
                }
            }
            
            if (true !== $it->getHintUsed()) {
                $it->setHintUsed(true);
                $em->merge($it);

                $em->flush();
            }
            
            $hint = $it->getQuestion()->getHints();
            
            return new Response($hint);
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function answerAction($id, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $form = $this->createForm(new AnswerType());
            $form->bind($request);
            $_data = $form->getData();
            $answer = $_data['answer'];
            
            if (null !== $answer) {
                $em = $this->getDoctrine()->getEntityManager();

                $question = $em->getRepository('CTFQuestBundle:Question')->find($id);

                $refAnswer = $question->getAnswerTemplate();

                /// TODO ///
                // Parse for modifiers and apply as closures on the data
                ////////////

                if (\strtolower($answer) === \strtolower($refAnswer)) {
                    // The answer is correct
                    $user = $this->get('security.context')->getToken()->getUser();
                    $userquest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);

                    $history = $userquest->getHistory();

                    foreach ($history as $item) {
                        if ($item->getQuestion()->getId() == $id) {
                            $it = $item;
                            break;
                        }
                    }

                    if (QuestUtil::$ATTEMPTING === $it->getStatus() || QuestUtil::$WRONG === $it->getStatus()) {
                        // First-attempt (or) re-attempt after being marked 'WRONG'
                        $it->setStatus(QuestUtil::$CORRECT);

                        $em->merge($it);
                        $em->flush();
                    }

                    // Advance quest stage,level and current stage,level
                    // - Check history to see if the next level/stage has already been
                    //   attempted
                    // - If it has, ignore updating quest_stage,level and simply
                    //   update current_stage,level to match quest_stage,level
                    // - Otherwise, simply advance quest,current_stage,level by one
                    $stage = $em->getRepository('CTFQuestBundle:Stage')->findByQuestion($id);
                    $questions = $stage->getQuestions();

                    $nextLevel = null;

                    foreach ($questions as $q) {
                        if ($q->getLevel() == ($question->getLevel() + 1)) {
                            $nextLevel = $q;
                            break;
                        }
                    }

                    if (null === $nextLevel) {
                        // Stage exhausted, move to the next one
                        $newStage = $em->getRepository('CTFQuestBundle:Stage')->find($stage->getId() + 1);
                        $nextLevel = $newStage->getQuestions()[0];
                        
                        //////
                        // THIS is where we check for the END of the CTF
                        //////
                        if (null === $nextLevel) {
                            
                        }
                    } else {
                        $newStage = $stage;
                    }

                    foreach ($history as $item) {
                        if ($item->getQuestion()->getId() == $nextLevel->getId()) {
                            if ($item->getStatus() === QuestUtil::$CORRECT) {
                                // next question is correct, move to quest_stage,level
                                $newStage = $userquest->getQuestStage();
                                $nextLevel = $userquest->getQuestLevel();
                            }
                            break;
                        }
                    }

                    $userquest->setQuestStage($newStage);
                    $userquest->setQuestLevel($nextLevel);
                    $userquest->setCurrentStage($newStage);
                    $userquest->setCurrentLevel($nextLevel);

                    $em->merge($userquest);
                    $em->flush();

                    $data = array(
                        'result' => 'success',
                        'message' => 'Congrats! That was the right answer!',
                        'next' => $nextLevel->getId()
                    );

                    return new Response(\json_encode($data));
                } else {
                    $data = array(
                        'result' => 'error',
                        'message' => 'Your answer was incorrect! Please try again.'
                    );

                    return new Response(\json_encode($data));
                }
            } else {
                $data = array(
                    'result' => 'error',
                    'message' => 'Your answer appears to be invalid. Please try again later.'
                );

                return new Response(\json_encode($data));
            }
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function rankAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $this->get('security.context')->getToken()->getUser();
            $rank = $em->getRepository('CTFQuestBundle:UserQuest')->getRankByUser($user->getId());
            
            $data = array(
                'rank' => $rank
            );
            
            return new Response(\json_encode($data));
        }
        
        return new Response('Bad Request!', 400);
    }
}