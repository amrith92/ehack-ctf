<?php

namespace CTF\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\TeamBundle\Entity\Team
 * 
 * @ORM\Entity
 * @ORM\Table(name="team")
 */
class Team {
    
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
     * @var string
     * 
     * @ORM\Column(name="name", type="string", length=30, nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="score", type="integer")
     */
    private $score;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="team_pic", type="string", nullable=true)
     */
    private $teamPic;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\TeamBundle\Entity\Team
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
     * @return \CTF\TeamBundle\Entity\Team
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
     * @param string $status
     * @return \CTF\TeamBundle\Entity\Team
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
     * @param integer $score
     * @return \CTF\TeamBundle\Entity\Team
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
     * @param string $pic
     * @return \CTF\TeamBundle\Entity\Team
     */
    public function setTeamPic($pic) {
        $this->teamPic = $pic;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getTeamPic() {
        return $this->teamPic;
    }
}