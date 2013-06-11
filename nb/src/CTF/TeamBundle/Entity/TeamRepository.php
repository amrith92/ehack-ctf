<?php

namespace CTF\TeamBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository {
    
    public function findRequestsByUserId($uid) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT t.name AS team_name, t.status AS team_status, r.created_timestamp AS created, r.status AS status FROM `team_member_request` r INNER JOIN `team_requests` e ON r.id=e.request_id INNER JOIN `team` t ON t.id=e.team_id WHERE user_id=" . $uid);
        $statement->execute();
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
}
