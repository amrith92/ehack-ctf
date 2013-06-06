<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class ZoneRepository extends EntityRepository {
    
    public function findStatesByCountryIdQueryBuilder($id) {
        $q = $this
            ->createQueryBuilder('z')
            ->where('z.country = :id')
            ->setParameter('id', $id)
        ;
        
        return $q;
    }
    
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
    
    public function findStatesByCountryCode($id) {
        $q = $this
            ->createQueryBuilder('z')
            ->innerJoin('CTF\UserBundle\Entity\Countries', 'c')
            ->where('c.id = z.country')
            ->andWhere('c.iso_code_2 = :id')
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
