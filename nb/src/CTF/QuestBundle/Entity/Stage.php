<?php

namespace CTF\QuestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\QuestBundle\Entity\Stage
 * 
 * @ORM\Entity
 * @ORM\Table(name="stages")
 */
class Stage {
    
    /**
     * @var intger
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="name", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $description;
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="\CTF\QuestBundle\Entity\Question", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="stages_levels",
     *      joinColumns={@ORM\JoinColumn(name="stage_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $questions;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\QuestBundle\Entity\Stage
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
     * @param string $name
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * 
     * @param string $desc
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function setDescription($desc) {
        $this->description = $desc;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    /**
     * 
     * @param ArrayCollection $questions
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function setQuestions($questions) {
        $this->questions = $questions;
        
        return $this;
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getQuestions() {
        return $this->questions;
    }
    
    /**
     * 
     * @param \CTF\QuestBundle\Entity\Question $question
     * @return \CTF\QuestBundle\Entity\Stage
     */
    public function addQuestion($question) {
        $this->questions[] = $question;
        
        return $this;
    }
}
