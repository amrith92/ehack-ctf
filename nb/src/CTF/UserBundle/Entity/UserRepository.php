<?php

namespace CTF\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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
}