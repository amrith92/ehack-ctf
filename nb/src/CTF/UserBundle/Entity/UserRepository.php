<?php

namespace CTF\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements UserProviderInterface {

    public function loadUserByUsername($username) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.email = :email')
                ->orWhere('u.username = :username')
                ->setParameter('email', $username)
                ->setParameter('username', $username)
                ->getQuery()
        ;

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                    'Unable to find an active admin with username "%s"', $username
            );

            throw new UsernameNotFoundException($message, null, 0, $e);
        }

        return $user;
    }
    
    public function findByUsername($username) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.email = :email')
                ->orWhere('u.username = :username')
                ->setParameter('email', $username)
                ->setParameter('username', $username)
                ->getQuery()
        ;

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = null;
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                    sprintf(
                            'Instances of "%s" are not supported.', $class
                    )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
    
    public function findOneByGoogleId($id) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.google_id = :id')
                ->setParameter('id', $id)
                ->getQuery()
        ;
        
        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = null;
        }

        return $user;
    }
    
    public function findOneByFacebookId($id) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.facebook_id = :id')
                ->setParameter('id', $id)
                ->getQuery()
        ;
        
        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = null;
        }

        return $user;
    }
    
    public function findOneByTwitterId($id) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.twitter_id = :id')
                ->setParameter('id', $id)
                ->getQuery()
        ;
        
        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = null;
        }

        return $user;
    }
    
    public function findOneByEmail($email) {
        $q = $this
                ->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
        ;
        
        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = null;
        }

        return $user;
    }
    
    public function createUsernameByEmail($email) {
        list($username) = explode('@', $email, 2);
        return $username;
    }
    
    public function countsInGenders() {
        $q = $this->createQueryBuilder('u')
            ->select('COUNT(u), u.gender')
            ->groupBy('u.gender')
            ->getQuery();
        
        try {
            $res = $q->getArrayResult();
        } catch (NoResultException $e) {
            $res = null;
        }
        
        return $res;
    }
    
    public function count() {
        $q = $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->where('u.verified=1')
            ->getQuery();
        
        try {
            $res = $q->getSingleScalarResult();
        } catch (NoResultException $e) {
            $res = null;
        }
        
        return $res;
    }
    
    public function findUsersByPartialUsername($name) {
         $q = $this->createQueryBuilder('u')
            ->where('u.username LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
        
        try {
            $ret = $q->getResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
    
    public function findUsersWithinBounds($bounds) {
        if (null === $bounds) {
            return null;
        }
        
        $sql = 'SELECT AsText( location ) AS location, username, fname, lname, dp FROM auth_users WHERE MBRContains(GeomFromText(\'POLYGON((';
        foreach($bounds as $k) {
                $sql .= (double)$k->lat . ' ' . (double)$k->lng . ',';
        }
        $sql .= (double)$bounds[0]->lat . ' ' . (double)$bounds[0]->lng;
        $sql .= '))\'), location)';
        
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        $ret = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return $ret;
    }
    
    public function getTopOrganizations($count) {
        $q = $this->createQueryBuilder('u')
            ->select('SUBSTRING_INDEX(o.name, \',\', 1) AS indexLabel, SUBSTRING_INDEX(o.name, \',\', 1) AS legendText, COUNT(u.org) AS y')
            ->innerJoin('CTFUserBundle:Organization', 'o', Expr\Join::WITH, 'u.org = o.id')
            ->groupBy('u.org')
            ->orderBy('y', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($count)
            ->getQuery();
        
        try {
            $ret = $q->getResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
    
    public function getBottomOrganizations($count) {
        $q = $this->createQueryBuilder('u')
            ->select('SUBSTRING_INDEX(o.name, \',\', 1) AS indexLabel, SUBSTRING_INDEX(o.name, \',\', 1) AS legendText, COUNT(u.org) AS y')
            ->innerJoin('CTFUserBundle:Organization', 'o', Expr\Join::WITH, 'u.org = o.id')
            ->groupBy('u.org')
            ->orderBy('y', 'ASC')
            ->setFirstResult(0)
            ->setMaxResults($count)
            ->getQuery();
        
        try {
            $ret = $q->getResult();
        } catch (NoResultException $e) {
            $ret = null;
        }
        
        return $ret;
    }
}