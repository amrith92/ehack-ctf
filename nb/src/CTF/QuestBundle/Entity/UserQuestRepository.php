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
    
    public function getGlobalRanks() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank, score, u.username AS username FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score DESC) as result INNER JOIN auth_users u ON result.user_id=u.id WHERE 1");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    public function getRankByUser($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score DESC) as result WHERE user_id=" . $uid);
        $ret = $statement->fetchColumn(0);
        
        return $ret;
    }
}
