<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserQuest
 *
 * @ORM\Table(name="user_quest")
 * @ORM\Entity
 */
class UserQuest
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quest_level", type="integer", nullable=false)
     */
    private $questLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_level", type="integer", nullable=false)
     */
    private $currentLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Questions", inversedBy="quest")
     * @ORM\JoinTable(name="user_quest_history",
     *   joinColumns={
     *     @ORM\JoinColumn(name="quest_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     *   }
     * )
     */
    private $question;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Stages
     *
     * @ORM\ManyToOne(targetEntity="Stages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quest_stage", referencedColumnName="id")
     * })
     */
    private $questStage;

    /**
     * @var \Stages
     *
     * @ORM\ManyToOne(targetEntity="Stages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="current_stage", referencedColumnName="id")
     * })
     */
    private $currentStage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->question = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set questLevel
     *
     * @param integer $questLevel
     * @return UserQuest
     */
    public function setQuestLevel($questLevel)
    {
        $this->questLevel = $questLevel;
    
        return $this;
    }

    /**
     * Get questLevel
     *
     * @return integer 
     */
    public function getQuestLevel()
    {
        return $this->questLevel;
    }

    /**
     * Set currentLevel
     *
     * @param integer $currentLevel
     * @return UserQuest
     */
    public function setCurrentLevel($currentLevel)
    {
        $this->currentLevel = $currentLevel;
    
        return $this;
    }

    /**
     * Get currentLevel
     *
     * @return integer 
     */
    public function getCurrentLevel()
    {
        return $this->currentLevel;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return UserQuest
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Add question
     *
     * @param \CTF\CommonBundle\Entity\Questions $question
     * @return UserQuest
     */
    public function addQuestion(\CTF\CommonBundle\Entity\Questions $question)
    {
        $this->question[] = $question;
    
        return $this;
    }

    /**
     * Remove question
     *
     * @param \CTF\CommonBundle\Entity\Questions $question
     */
    public function removeQuestion(\CTF\CommonBundle\Entity\Questions $question)
    {
        $this->question->removeElement($question);
    }

    /**
     * Get question
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set user
     *
     * @param \CTF\CommonBundle\Entity\Users $user
     * @return UserQuest
     */
    public function setUser(\CTF\CommonBundle\Entity\Users $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \CTF\CommonBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set questStage
     *
     * @param \CTF\CommonBundle\Entity\Stages $questStage
     * @return UserQuest
     */
    public function setQuestStage(\CTF\CommonBundle\Entity\Stages $questStage = null)
    {
        $this->questStage = $questStage;
    
        return $this;
    }

    /**
     * Get questStage
     *
     * @return \CTF\CommonBundle\Entity\Stages 
     */
    public function getQuestStage()
    {
        return $this->questStage;
    }

    /**
     * Set currentStage
     *
     * @param \CTF\CommonBundle\Entity\Stages $currentStage
     * @return UserQuest
     */
    public function setCurrentStage(\CTF\CommonBundle\Entity\Stages $currentStage = null)
    {
        $this->currentStage = $currentStage;
    
        return $this;
    }

    /**
     * Get currentStage
     *
     * @return \CTF\CommonBundle\Entity\Stages 
     */
    public function getCurrentStage()
    {
        return $this->currentStage;
    }
}