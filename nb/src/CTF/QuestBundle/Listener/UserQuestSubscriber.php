<?php

namespace CTF\QuestBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use CTF\QuestBundle\Entity\UserQuest;
use CTF\TeamBundle\Entity\Team;
use CTF\QuestBundle\Util\QuestUtil;
use CTF\TeamBundle\Util\TeamRequestStatus;

class UserQuestSubscriber implements EventSubscriber {
    
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }
    
    public function postUpdate(LifecycleEventArgs $args) {
        $this->calculateScore($args);
    }
    
    public function postPersist(LifecycleEventArgs $args) {
        $this->calculateScore($args);
    }
    
    public function calculateScore(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        
        if ($entity instanceof UserQuest) {
            $history = $entity->getHistory();
            
            $score = 0;
            foreach ($history as $h) {
                if ($h->getStatus() == QuestUtil::$CORRECT) {
                    $score = $score + 10;
                    if (true === $h->getHintUsed()) {
                        $score = $score - 1;
                    }
                }
            }
            
            // Award 5 points extra per referral
            $invites = $entity->getUser()->getInvitations()->count();

            if ($invites > 0) {
                $score = $score + 5 * $invites;
            }
            
            $entity->setScore($score);
            
            {
                $teamrepo = $em->getRepository('CTFTeamBundle:Team');
                $team = $teamrepo->find($teamrepo->findTeamIdByUserId($entity->getUser()->getId()));
                $requests = $team->getRequests();
                $questrepo = $em->getRepository('CTFQuestBundle:UserQuest');
            
                $sum = 0;
                $count = 0;
                foreach ($requests as $r) {
                    if ($r->getStatus() == TeamRequestStatus::$ACCEPTED || $r->getStatus() == TeamRequestStatus::$ACCEPTEDANDADMIN) {
                        $quest = $questrepo->findByUser($r->getUser());
                        if (null != $quest) {
                            $sum = $sum + $quest->getScore();
                            ++$count;
                        }
                        
                        // Award 5 points extra per referral
                        $invites = $r->getUser()->getInvitations()->count();
                        
                        if ($invites > 0) {
                            $sum = $sum + 5 * $invites;
                        }
                    }
                }

                if ($sum != 0 && $count != 0) {
                    $teamscore = \floor($sum / $count);
                    $team->setScore($teamscore);

                    $em->merge($team);
                }
            }
            
            $em->merge($entity);
            $em->flush();
        }
    }
}
