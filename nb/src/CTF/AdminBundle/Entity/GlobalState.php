<?php

namespace CTF\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\AdminBundle\Entity\GlobalState
 * 
 * @ORM\Entity
 * @ORM\Table(name="global_state")
 */
class GlobalState {
    
    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="enable_ctf", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $enableCtf;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="enable_chat", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $enableChat;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="enable_stats", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $enableStats;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\AdminBundle\Entity\GlobalState
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
     * @param boolean $enable
     * @return \CTF\AdminBundle\Entity\GlobalState
     */
    public function setEnableCtf($enable) {
        $this->enableCtf = $enable;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCtfEnabled() {
        return $this->enableCtf;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getEnableCtf() {
        return $this->enableCtf;
    }
    
    /**
     * 
     * @param boolean $enable
     * @return \CTF\AdminBundle\Entity\GlobalState
     */
    public function setEnableChat($enable) {
        $this->enableChat = $enable;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isChatEnabled() {
        return $this->enableChat;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getEnableChat() {
        return $this->enableChat;
    }
    
    /**
     * 
     * @param boolean $enable
     * @return \CTF\AdminBundle\Entity\GlobalState
     */
    public function setEnableStats($enable) {
        $this->enableStats = $enable;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isStatsEnabled() {
        return $this->enableStats;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getEnableStats() {
        return $this->enableStats;
    }
}
