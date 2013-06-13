<?php

namespace CTF\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * \CTF\TeamBundle\Entity\Team
 * 
 * @ORM\Entity(repositoryClass="\CTF\TeamBundle\Entity\TeamRepository")
 * @ORM\Table(name="team")
 * @UniqueEntity(fields={"name"}, message="Team name already selected!")
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
     * @Assert\NotBlank()
     * @Assert\Length(max=30)
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
     * @Assert\Blank()
     * @Assert\Url()
     */
    private $teamPic;
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="\CTF\TeamBundle\Entity\TeamMemberRequest", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="team_requests",
     *      joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="request_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $requests;
    
    public function __construct() {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
    
    /**
     * 
     * @param ArrayCollection $requests
     * @return \CTF\TeamBundle\Entity\Team
     */
    public function setRequests($requests) {
        $this->requests = $requests;
        
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getRequests() {
        return $this->requests;
    }
    
    /**
     * 
     * @param \CTF\TeamBundle\Entity\TeamMemberRequest $request
     * @return \CTF\TeamBundle\Entity\Team
     */
    public function addRequest($request) {
        $this->requests[] = $request;
        
        return $this;
    }
}