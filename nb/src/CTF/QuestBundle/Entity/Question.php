<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\QuestBundle\Entity\Question
 * 
 * @ORM\Entity
 * @ORM\Table(name="questions")
 */
class Question {
    
    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="level", type="integer", nullable=false)
     */
    private $level;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="title", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $title;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="content", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $content;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="answer_tpl", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $answerTemplate;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="hints", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $hints;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\QuestBundle\Entity\Question
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
     * @param integer $level
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function setLevel($level) {
        $this->level = $level;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getLevel() {
        return $this->level;
    }
    
    /**
     * 
     * @param string $title
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function setTitle($title) {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * 
     * @param string $content
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function setContent($content) {
        $this->content =$content;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * 
     * @param string $tpl
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function setAnswerTemplate($tpl) {
        $this->answerTemplate = $tpl;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getAnswerTemplate() {
        return $this->answerTemplate;
    }
    
    /**
     * 
     * @param string $hints
     * @return \CTF\QuestBundle\Entity\Question
     */
    public function setHints($hints) {
        $this->hints = $hints;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getHints() {
        return $this->hints;
    }
}
