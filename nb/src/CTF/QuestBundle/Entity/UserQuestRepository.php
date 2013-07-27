<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserQuestRepository extends EntityRepository {
    
    /**
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return null | \CTF\QuestBundle\Entity\UserQuest
     */
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
    
    /**
     * 
     * @return array
     */
    public function getGlobalRanks() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank, score, u.username AS username FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score DESC) as result INNER JOIN auth_users u ON result.user_id=u.id WHERE 1");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    /**
     * 
     * @param integer $uid
     * @return array
     */
    public function getRankByUser($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score DESC) as result WHERE user_id=" . $uid);
        $ret = $statement->fetchColumn(0);
        
        return $ret;
    }
    
    /**
     * 
     * @return array
     */
    public function getTopTwenty() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank, score, u.username AS username, u.id AS id, u.dp AS dp FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score DESC) as result INNER JOIN auth_users u ON result.user_id=u.id WHERE 1 LIMIT 0,20");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    /**
     * 
     * @return array
     */
    public function getBottomTwenty() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank, score, u.username AS username, u.id AS id, u.dp AS dp FROM (SELECT @rownum := @rownum + 1 AS rank, score, user_id FROM user_quest ORDER BY score ASC) as result INNER JOIN auth_users u ON result.user_id=u.id WHERE 1 LIMIT 0,20");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    public function countsPerStage() {
        $q = $this->createQueryBuilder('q')
            ->select('COUNT(q)')
            ->distinct()
            ->groupBy('q.questStage')
            ->orderBy('q.questStage', 'DESC')
            ->addSelect('COUNT(s) - 1')
            ->addGroupBy('s.id')
            ->leftJoin('q.questStage', 's', 'WITH', 's.id >= 1')
            ->getQuery();
        
        try {
            $ret = $q->getArrayResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
}
