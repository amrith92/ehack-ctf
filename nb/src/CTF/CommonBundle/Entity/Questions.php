<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questions
 *
 * @ORM\Table(name="questions")
 * @ORM\Entity
 */
class Questions
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
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_tpl", type="text", nullable=false)
     */
    private $answerTpl;

    /**
     * @var string
     *
     * @ORM\Column(name="hints", type="text", nullable=false)
     */
    private $hints;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Stages", mappedBy="question")
     */
    private $stage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UserQuest", mappedBy="question")
     */
    private $quest;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stage = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quest = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Questions
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Questions
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set answerTpl
     *
     * @param string $answerTpl
     * @return Questions
     */
    public function setAnswerTpl($answerTpl)
    {
        $this->answerTpl = $answerTpl;
    
        return $this;
    }

    /**
     * Get answerTpl
     *
     * @return string 
     */
    public function getAnswerTpl()
    {
        return $this->answerTpl;
    }

    /**
     * Set hints
     *
     * @param string $hints
     * @return Questions
     */
    public function setHints($hints)
    {
        $this->hints = $hints;
    
        return $this;
    }

    /**
     * Get hints
     *
     * @return string 
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Add stage
     *
     * @param \CTF\CommonBundle\Entity\Stages $stage
     * @return Questions
     */
    public function addStage(\CTF\CommonBundle\Entity\Stages $stage)
    {
        $this->stage[] = $stage;
    
        return $this;
    }

    /**
     * Remove stage
     *
     * @param \CTF\CommonBundle\Entity\Stages $stage
     */
    public function removeStage(\CTF\CommonBundle\Entity\Stages $stage)
    {
        $this->stage->removeElement($stage);
    }

    /**
     * Get stage
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Add quest
     *
     * @param \CTF\CommonBundle\Entity\UserQuest $quest
     * @return Questions
     */
    public function addQuest(\CTF\CommonBundle\Entity\UserQuest $quest)
    {
        $this->quest[] = $quest;
    
        return $this;
    }

    /**
     * Remove quest
     *
     * @param \CTF\CommonBundle\Entity\UserQuest $quest
     */
    public function removeQuest(\CTF\CommonBundle\Entity\UserQuest $quest)
    {
        $this->quest->removeElement($quest);
    }

    /**
     * Get quest
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuest()
    {
        return $this->quest;
    }
}