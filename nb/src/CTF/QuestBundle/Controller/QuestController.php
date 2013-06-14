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
    public function dashboardAction(Request $request)
    {
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
        
        return $this->render('CTFQuestBundle:Quest:dashboard.html.twig', array(
            'user' => $user,
            'stages' => $stages
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
                $question = $quest->getQuestLevel();
            }
            
            $answer = $this->createForm(new AnswerType());

            return $this->render('CTFQuestBundle:Quest:question.display.html.twig', array(
                'answer' => $answer->createView(),
                'question' => $question
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
                    // Return question
                    $answer = $this->createForm(new AnswerType());
                    $message = $this->render('CTFQuestBundle:Quest:question.display.html.twig', array(
                        'question' => $question,
                        'answer' => $answer->createView()
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
                'title' => $userquest->getCurrentLevel()->getTitle()
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
}