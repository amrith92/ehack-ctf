<?php

namespace CTF\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\TeamBundle\Entity\TeamMemberRequest
 * 
 * @ORM\Entity
 * @ORM\Table("team_member_request")
 */
class TeamMemberRequest {
    
    /**
     *
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var \CTF\UserBundle\Entity\User
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    private $message;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="updated_timestamp", type="datetimetz")
     */
    private $updatedTimestamp;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created_timestamp", type="datetimetz")
     */
    private $createdTimestamp;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setUser($user) {
        $this->user = $user;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\UserBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * 
     * @param string $status
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setStatus($status) {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * 
     * @param string $message
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setMessage($message) {
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * 
     * @param \DateTime $timestamp
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setUpdatedTimestamp($timestamp) {
        $this->updatedTimestamp = $timestamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getUpdatedTimestamp() {
        return $this->updatedTimestamp;
    }
    
    /**
     * 
     * @param \DateTime $timestamp
     * @return \CTF\TeamBundle\Entity\TeamMemberRequest
     */
    public function setCreatedTimestamp($timestamp) {
        $this->createdTimestamp = $timestamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getCreatedTimestamp() {
        return $this->createdTimestamp;
    }
}
