<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\QuestBundle\Entity\QuestHistoryItem
 * 
 * @ORM\Entity
 * @ORM\Table(name="user_quest_history_item")
 */
class QuestHistoryItem {
    
    /**
     *
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var \CTF\QuestBundle\Entity\Question
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\QuestBundle\Entity\Question", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="status", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $status;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="used_hint", type="boolean", nullable=false)
     */
    private $hintUsed;
    
    /**
     *
     * @var \DateTime
     * 
     * @ORM\Column(name="attempted_timestamp", type="datetimetz")
     */
    private $attemptedTimestamp;
    
    /**
     *
     * @var \DateTime
     * 
     * @ORM\Column(name="first_attempt_timestamp", type="datetimetz")
     */
    private $firstAttemptTimestamp;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
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
     * @param \CTF\QuestBundle\Entity\Question $question
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
     */
    public function setQuestion($question) {
        $this->question = $question;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function getQuestion() {
        return $this->question;
    }
    
    /**
     * 
     * @param string $status
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
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
     * @param boolean $used
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
     */
    public function setHintUsed($used) {
        $this->hintUsed = $used;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getHintUsed() {
        return $this->hintUsed;
    }
    
    /**
     * 
     * @param \DateTime $timestamp
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
     */
    public function setAttemptedTimestamp($timestamp) {
        $this->attemptedTimestamp = $timestamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function  getAttemptedTimestamp() {
        return $this->attemptedTimestamp;
    }
    
    /**
     * 
     * @param \DateTime $timestamp
     * @return \CTF\QuestBundle\Entity\QuestHistoryItem
     */
    public function setFirstAttemptTimestamp($timestamp) {
        $this->firstAttemptTimestamp = $timestamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getFirstAttemptedTimestamp() {
        return $this->firstAttemptTimestamp;
    }
}
