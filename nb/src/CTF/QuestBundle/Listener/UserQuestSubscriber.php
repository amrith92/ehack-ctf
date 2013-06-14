<?php

namespace CTF\QuestBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use CTF\QuestBundle\Entity\UserQuest;
use CTF\QuestBundle\Util\QuestUtil;

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
            
            $entity->setScore($score);
            
            $em->merge($entity);
            $em->flush();
        }
    }
}
