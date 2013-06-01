<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GlobalState
 *
 * @ORM\Table(name="global_state")
 * @ORM\Entity
 */
class GlobalState
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_ctf", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $enableCtf;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_chat", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $enableChat;



    /**
     * Set enableCtf
     *
     * @param boolean $enableCtf
     * @return GlobalState
     */
    public function setEnableCtf($enableCtf)
    {
        $this->enableCtf = $enableCtf;
    
        return $this;
    }

    /**
     * Get enableCtf
     *
     * @return boolean 
     */
    public function getEnableCtf()
    {
        return $this->enableCtf;
    }

    /**
     * Set enableChat
     *
     * @param boolean $enableChat
     * @return GlobalState
     */
    public function setEnableChat($enableChat)
    {
        $this->enableChat = $enableChat;
    
        return $this;
    }

    /**
     * Get enableChat
     *
     * @return boolean 
     */
    public function getEnableChat()
    {
        return $this->enableChat;
    }
}