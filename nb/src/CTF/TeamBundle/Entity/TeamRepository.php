<?php

namespace CTF\TeamBundle\Entity;

use Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\NoResultException;
use CTF\TeamBundle\Util\TeamRequestStatus;

class TeamRepository extends EntityRepository {
    
    public function findRequestsByUserId($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT t.name AS team_name, t.status AS team_status, r.created_timestamp AS created, r.status AS status FROM `team_member_request` r INNER JOIN `team_requests` e ON r.id=e.request_id INNER JOIN `team` t ON t.id=e.team_id WHERE user_id=" . $uid);
        $statement->execute();
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    public function findAcceptedRequestByUserId($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT t.name AS team_name FROM `team_member_request` r INNER JOIN `team_requests` e ON r.id=e.request_id INNER JOIN `team` t ON t.id=e.team_id WHERE user_id=" . $uid . " AND (r.status='" . TeamRequestStatus::$ACCEPTED . "' OR r.status='" . TeamRequestStatus::$ACCEPTEDANDADMIN . "')");
        $statement->execute();
        $ret = $statement->fetchColumn(0);
        
        return $ret;
    }
    
    public function findTeamIdByUserId($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT t.id AS team_id FROM `team_member_request` r INNER JOIN `team_requests` e ON r.id=e.request_id INNER JOIN `team` t ON t.id=e.team_id WHERE user_id=" . $uid . " AND (r.status='" . TeamRequestStatus::$ACCEPTED . "' OR r.status='" . TeamRequestStatus::$ACCEPTEDANDADMIN . "')");
        $statement->execute();
        $ret = $statement->fetchColumn(0);
        
        return $ret;
    }
    
    public function findAdminedByUserId($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT t.id FROM `team_member_request` r INNER JOIN `team_requests` e ON r.id=e.request_id INNER JOIN `team` t ON t.id=e.team_id WHERE user_id=" . $uid . " AND r.status='" . TeamRequestStatus::$ACCEPTEDANDADMIN . "'");
        $statement->execute();
        $ret = $statement->fetchColumn(0);
        
        return $ret;
    }
    
    public function countOfTeams() {
        $q = $this->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->getQuery();
        
        try {
            $ret = $q->getSingleScalarResult();
        } catch (NoResultException $e) {
            $ret = 0;
        }
        
        return $ret;
    }
    
    public function getGlobalRanks() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->executeQuery('SET @rownum := 0');
        $statement = $connection->executeQuery("SELECT rank, score, name, status FROM (SELECT @rownum := @rownum + 1 AS rank, score, name, status FROM team ORDER BY score DESC) as result WHERE 1");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    public function getSurrounding($score) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->executeQuery("SELECT name, `status`, score FROM (SELECT t.*, @n1:=@n1 + 1 num, @n2:=IF(score = " . $score . ", @n1, @n2) pos FROM team t, (SELECT @n1:=0, @n2:=0) n ORDER BY t.id) q WHERE @n2 >= num - 2 AND @n2 <= num + 2;");
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
}
