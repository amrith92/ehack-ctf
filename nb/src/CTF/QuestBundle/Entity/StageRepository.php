<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class StageRepository extends EntityRepository {
    
    public function findFirst() {
        $q = $this->createQueryBuilder('q')
            ->select('q')
            ->orderBy('q.id', 'ASC')
            ->getQuery();
        
        try {
            $ret = $q->getResult()[0];
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
    
    public function findByQuestion($qid) {
        $q = $this->createQueryBuilder('s');
        $qu = $q->select('s')
                ->innerJoin('s.questions', 'q', 'WITH', 'q.id = :id')
                ->setParameter('id', $qid)
                ->getQuery();
        
        try {
            $ret = $qu->getSingleResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
}

