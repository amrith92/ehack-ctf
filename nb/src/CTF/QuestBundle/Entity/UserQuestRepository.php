<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserQuestRepository extends EntityRepository {
    
    public function findByUser($user) {
        $q = $this->createQueryBuilder('q')
            ->select('q')
            ->where('q.user=:user')
            ->setParameter('user', $user)
            ->getQuery();
        
        try {
            $ret = $q->getSingleResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
}
