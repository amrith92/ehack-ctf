<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * CTF\UserBundle\Entity\User
 * 
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="CTF\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;
    
    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;
    
    /**
     * @ORM\Column(name="sms_verify", type="boolean")
     */
    private $isVerified;
    
    /**
     * @ORM\Column(name="is_banned", type="boolean")
     */
    private $isBanned;
    
    public function __construct()
    {
        $this->isVerified = true;
        $this->isBanned = false;
        $this->salt = md5(uniqid(null, true));
    }
    
    /**
     * Sets ID
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * Returns ID
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->email;
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt() {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }
    
    /**
     * @inheritDoc
     */
    public function getRoles() {
        return array('ROLE_USER');
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        ;
    }
    
    /**
     * @inheritDoc
     */
    public function isAccountNonExpired() {
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function isAccountNonLocked() {
        return !$this->isBanned;
    }
    
    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired() {
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function isEnabled() {
        return $this->isVerified;
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
        ));
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id
        ) = unserialize($serialized);
    }
    
    /**
     * @see \Symfony\Component\Security\Core\User\EquatableInterface::isEqualTo()
     */
    public function isEqualTo(UserInterface $user) {
        return $this->email === $user->getUsername();
    }
}