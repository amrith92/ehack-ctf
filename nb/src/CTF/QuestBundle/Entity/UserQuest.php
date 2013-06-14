<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * \CTF\QuestBundle\Entity
 * 
 * @ORM\Entity(repositoryClass="\CTF\QuestBundle\Entity\UserQuestRepository")
 * @ORM\Table(name="user_quest")
 * @UniqueEntity(fields={"user"}, message="User already registered in Quest!")
 */
class UserQuest {
    
    /**
     *
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=true, unique=true)
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
     * @var \CTF\QuestBundle\Entity\Stage
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\QuestBundle\Entity\Stage")
     * @ORM\JoinColumn(name="quest_stage", referencedColumnName="id")
     */
    private $questStage;
    
    /**
     *
     * @var \CTF\QuestBundle\Entity\Question
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\QuestBundle\Entity\Question")
     * @ORM\JoinColumn(name="quest_level", referencedColumnName="id")
     */
    private $questLevel;
    
    /**
     *
     * @var \CTF\QuestBundle\Entity\Stage
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\QuestBundle\Entity\Stage")
     * @ORM\JoinColumn(name="current_stage", referencedColumnName="id")
     */
    private $currentStage;
    
    /**
     *
     * @var \CTF\QuestBundle\Entity\Question
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\QuestBundle\Entity\Question")
     * @ORM\JoinColumn(name="current_level", referencedColumnName="id")
     */
    private $currentLevel;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="score", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $score;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="\CTF\QuestBundle\Entity\QuestHistoryItem", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="user_quest_history",
     *      joinColumns={@ORM\JoinColumn(name="quest_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $history;
    
    public function __construct() {
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * 
     * @param integer $id
     * @return \CTF\QuestBundle\Entity\UserQuest
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
     * @return \CTF\QuestBundle\Entity\UserQuest
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
     * @param \CTF\QuestBundle\Entity\Stage $stage
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setQuestStage($stage) {
        $this->questStage = $stage;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function getQuestStage() {
        return $this->questStage;
    }
    
    /**
     * 
     * @param \CTF\QuestBundle\Entity\Question $level
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setQuestLevel($level) {
        $this->questLevel = $level;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function getQuestLevel() {
        return $this->questLevel;
    }
    
    /**
     * 
     * @param \CTF\QuestBundle\Entity\Stage $stage
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setCurrentStage($stage) {
        $this->currentStage = $stage;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function getCurrentStage() {
        return $this->currentStage;
    }
    
    /**
     * 
     * @param \CTF\QuestBundle\Entity\Question $level
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setCurrentLevel($level) {
        $this->currentLevel = $level;
        
        return $this;
    }
    
    /**
     * 
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function getCurrentLevel() {
        return $this->currentLevel;
    }
    
    /**
     * 
     * @param integer $score
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setScore($score) {
        $this->score = $score;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getScore() {
        return $this->score;
    }
    
    /**
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $history
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function setHistory($history) {
        $this->history = $history;
        
        return $this;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getHistory() {
        return $this->history;
    }
    
    /**
     * 
     * @param \CTF\QuestBundle\Entity\QuestHistoryItem $item
     * @return \CTF\QuestBundle\Entity\UserQuest
     */
    public function addHistoryItem($item) {
        $this->history[] = $item;
        
        return $this;
    }
}
