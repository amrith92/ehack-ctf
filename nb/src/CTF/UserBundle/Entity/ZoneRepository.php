<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class ZoneRepository extends EntityRepository {
    
    public function findStatesByCountryId($id) {
        $q = $this
            ->createQueryBuilder('z')
            ->where('z.country = :id')
            ->setParameter('id', $id)
            ->getQuery()
        ;
        
        try {
            $states = $q->getResult();
        } catch (NoResultException $e) {
            return null;
        }

        return $states;
    }
}
