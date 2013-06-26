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

class QuestController extends Controller {

    public function dashboardAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        // User is not verified
        if (false == $user->isVerified()) {
            return $this->redirect($this->generateUrl('ctf_registration_homepage'));
        }
        
        // User hasn't given us all the details
        if (false == $this->get('ctf_user_util')->hasFullyRegistered($user)) {
            $this->get('session')->getFlashBag()->add('notice', "You've logged in successfully, but your profile isn't quite complete yet! Take a moment to fill in necessary details.");
            return $this->redirect($this->generateUrl('ctf_user_edit_profile'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $cache = $this->get('ctf_cache');
        
        if ($cache->has(\md5($user->getId() . '_teamname'))) {
            $teamname = $cache->get(\md5($user->getId() . '_teamname'));
        } else {
            $teamname = $em->getRepository('CTFTeamBundle:Team')->findAcceptedRequestByUserId($user->getId());
        }
        
        if (null == $teamname) {
            $this->get('session')->getFlashBag()->add('notice', "Looks like you aren't a part of a team yet! You cannot join the CTF Event without creating or selecting a team.");
            return $this->redirect($this->generateUrl('ctf_team_select'));
        } else {
            if (!$cache->has(\md5($user->getId() . '_teamname'))) {
                $cache->store(\md5($user->getId() . '_teamname'), $teamname);
            }
        }
        
        if ($cache->has(\md5($user->getId() . '_teamid'))) {
            $id = $cache->get(\md5($user->getId() . '_teamid'));
            $team = $em->getRepository('CTFTeamBundle:Team')->find($id);
        } else {
            $team = $em->getRepository('CTFTeamBundle:Team')->findOneBy(array(
                'name' => $teamname
            ));
            $cache->store(\md5($user->getId() . '_teamid'), $team->getId());
        }

        $stages = $em->getRepository('CTFQuestBundle:Stage')->findAll();

        $salt = $this->container->getParameter('secret');
        $this->get('session')->set('__MYREF', $this->get('ctf_referral.cryptor')->encrypt($user->getId()));

        $response = $this->render('CTFQuestBundle:Quest:dashboard.html.twig', array(
                    'user' => $user,
                    'stages' => $stages,
                    'teamname' => \md5($teamname . $salt),
                    'team' => $team
                ));
        $response->mustRevalidate();
        $response->expire();
        
        return $response;
    }

    public function continueQuestAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if ($request->isXmlHttpRequest() && $request->isMethod('GET')) {
            $user = $this->get('security.context')->getToken()->getUser();
            
            if ($user->isLocked()) {
                // The user has been banned
                return new Response('<div class="alert alert-error">You have been banned by the administrator!</div>');
            }
            
            $em = $this->getDoctrine()->getEntityManager();
            
            $cache = $this->get('ctf_cache');
            $teamid = $cache->get(\md5($user->getId() . '_teamid'));
            $team = $em->getRepository('CTFTeamBundle:Team')->find($teamid);

            if (false == $team->getActive()) {
                return new Response('<div class="alert alert-error">Your team has been banned from participating further for the time-being. Please try again later.</div>');
            }

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
                $quest->setCompleted(false);
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
                if ($quest->getCompleted() == true) {
                    return $this->redirect($this->generateUrl('ctf_quest_finish'));
                }
                
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
            $dir = __DIR__ . '/../../../../web/uploads/questions/' . md5($salt . '/s' . $quest->getCurrentStage()->getId() . $salt . '/l' . $question->getLevel() . $salt);
            if (\file_exists($dir)) {
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
            
            if ($user->isLocked()) {
                // The user has been banned
                $data = array(
                    'result' => 'error',
                    'message' => 'You have been banned by the administrator!'
                );
                return new Response(\json_encode($data));
            }
            
            $cache = $this->get('ctf_cache');
            $teamid = $cache->get(\md5($user->getId() . '_teamid'));
            $team = $em->getRepository('CTFTeamBundle:Team')->find($teamid);

            if (false == $team->getActive()) {
                $data = array(
                    'result' => 'error',
                    'message' => "Your team has been banned from participating further for the time-being. Please try again later."
                );
                return new Response(\json_encode($data));
            }
            
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
                    $dir = __DIR__ . '/../../../../web/uploads/questions/' . md5($salt . '/s' . $stage->getId() . $salt . '/l' . $question->getLevel() . $salt);
                    if (\file_exists($dir)) {
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
                    
                    $response = new Response(\json_encode($data));
                    $response->mustRevalidate();

                    return $response;
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

            $response = new Response($hint);
            $response->mustRevalidate();
            
            return $response;
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
                $user = $this->get('security.context')->getToken()->getUser();
                
                if ($user->isLocked()) {
                    // The user has been banned
                    return new Response('<div class="alert alert-error">You have been banned by the administrator!</div>');
                }
                
                // Check if the team has been banned
                $cache = $this->get('ctf_cache');
                $teamid = $cache->get(\md5($user->getId() . '_teamid'));
                $team = $em->getRepository('CTFTeamBundle:Team')->find($teamid);
                
                if (false == $team->getActive()) {
                    $data = array(
                        'result' => 'error',
                        'message' => "Your team has been banned from participating further for the time-being. Please try again later."
                    );
                    return new Response(\json_encode($data));
                }

                $question = $em->getRepository('CTFQuestBundle:Question')->find($id);

                $refAnswer = $question->getAnswerTemplate();

                /// TODO ///
                // Parse for modifiers and apply as closures on the data
                ////////////
                /// Step 1: Find valid BBCODE [ddynamic]...[/ddynamic].
                ///         The correct answer must be placed after the
                ///         [ddynamic]...[/ddynamic] section.
                /// Step 2: If [ddynamic]...[/ddynamic] exists, parse
                ///         section [params]...[/params], if it exists
                ///         Params MUST be a comma-separated list of
                ///         valid params.
                /// Step 3: Call custom anonymous function and evaluate result
                /// Step 4: Continue validation
                /// VALID PARAMS:
                ///     name:       Full Name
                ///     firstname:  First Name
                ///     lastname:   Last Name
                ///     id:         User Id
                ///     teamname:   Team Name
                ///     teamid:     Team Id
                ///     answer:     User's current answer
                ///     number:     User's phone-number (as entered)
                $matches = null;
                if (\preg_match("/.*\[ddynamic\][\s]*([\d\w\s\[\],\/\(\)\\\\\$\=\!\#\;\_\^\&\*\%\@\:\`]*)[\s]*\[\/ddynamic\][\s]*(.*)/s", $refAnswer, $matches)) {
                    $pmatches = null;
                    if (\preg_match("/.*\[params\][\s]*([\d\w\s,]*)[\s]*\[\/params\][\s]*(.*)/s", $matches[1], $pmatches)) {
                        $params = \explode(',', \trim($pmatches[1]));
                        $src = 'extract($args);' . \trim($pmatches[2]);
                        
                        $args = null;
                        foreach ($params as $p) {
                            switch ($p) {
                            case 'name':
                                $args['name'] = $user->getFname() . ' ' . $user->getLname();
                            break;
                            case 'firstname':
                                $args['firstname'] = $user->getFname();
                            break;
                            case 'lastname':
                                $args['lastname'] = $user->getLname();
                            break;
                            case 'id':
                                $args['id'] = $user->getId();
                            break;
                            case 'teamname':
                                $args['teamname'] = $em->getRepository('CTFTeamBundle:Team')->findAcceptedRequestByUserId($user->getId());
                            break;
                            case 'teamid':
                                $args['teamid'] = $em->getRepository('CTFTeamBundle:Team')->findTeamIdByUserId($user->getId());
                            break;
                            case 'answer':
                                $args['answer'] = $answer;
                            break;
                            case 'number':
                                $args['number'] = $user->getPhone();
                            break;
                            }
                        }
                        
                        $fn = \create_function('$args', $src);
                        
                        $answer = $fn($args);
                        $refAnswer = \trim($matches[2]);
                    } else {
                        $src = \trim($matches[1]);
                        $fn = function() use ($src) {
                            return eval($src);
                        };
                        
                        $refAnswer = $fn();
                    }
                }

                if ($answer == $refAnswer) {
                    // The answer is correct
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
                    
                    if (null == $nextLevel) {
                        // Stage exhausted, move to the next one
                        $newStage = $em->getRepository('CTFQuestBundle:Stage')->nextStage($stage->getId());
                        
                        if (null != $newStage) {
                            $questions = $newStage->getQuestions();
                            if (null != $questions[0]) {
                                $nextLevel = $questions[0];
                            } else {
                                //////
                                // No more levels in this stage - END of CTF
                                //////
                                $userquest->setQuestStage($newStage);
                                $userquest->setCurrentStage($newStage);
                                $userquest->setCompleted(true);
                                
                                $em->merge($userquest);
                                $em->flush();
                                
                                $data = array(
                                    'result' => 'finish',
                                    'message' => "Congrats! You've reached the end of your journey!"
                                );
                                return new Response(\json_encode($data));
                            }
                        } else {
                            //////
                            // THIS is where we check for the END of the CTF
                            //////
                            $userquest->setCompleted(true);
                            $em->merge($userquest);
                            $em->flush();
                            
                            $data = array(
                                'result' => 'finish',
                                'message' => "Congrats! You've reached the end of your journey!"
                            );
                            return new Response(\json_encode($data));
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
    
    public function finishAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CTFQuestBundle:Quest:finish.html.twig');
        }
        
        return new Response('Bad Request!', 400);
    }
    
    public function visitAction($filename, Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $userquest = $em->getRepository('CTFQuestBundle:UserQuest')->findByUser($user);
        
        $stage = $userquest->getCurrentStage();
        $question = $userquest->getCurrentLevel();
        $salt = $this->container->getParameter('secret');
        
        $hash = \md5($salt . '/s' . $stage->getId() . $salt . '/l' . $question->getLevel() . $salt);
        $dir = __DIR__ . '/../../../../web/uploads/questions/' . $hash;

        if (null == $filename) {
            $file = $dir . '/index.html';
        } else {
            $file = $dir . '/' . $filename;
        }
        
        if (!\file_exists($file)) {
            $file = $dir . '/index.php';
        } else {
            $file = $dir . '/' . $filename;
        }
        
        $finfo = \finfo_open(FILEINFO_MIME_TYPE);
        $content = file_get_contents($file);
        
        $response = new Response();
        
        $response->headers->set('Content-Type', \finfo_file($finfo, $file));
        $response->headers->set('Content-Transfer-Encoding','binary');
        $response->headers->set('Expires','0');
        $response->headers->set('Pragma','public');
        $response->headers->set('Content-Length','' . \filesize($file));
        
        \finfo_close($finfo);
        
        if ('php' == \pathinfo($file)['extension']) {
            \ob_start();
            include $file;
            $content = \ob_get_contents();
            \ob_end_clean();
        }
        $response->setContent($content);
        
        return $response;
    }
    
    public function shareCertificateOnTwitterAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        
        return $this->render('CTFQuestBundle:Quest:twitter.share.html.twig');
    }

}