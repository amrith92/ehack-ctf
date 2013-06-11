<?php

namespace CTF\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $enableCtf;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="enable_chat", type="boolean", nullable=false)
     */
    private $enableChat;
    
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
}
