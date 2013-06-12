<?php

namespace CTF\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \CTF\AdminBundle\Entity\Announcement
 * 
 * @ORM\Entity
 * @ORM\Table(name="announce")
 */
class Announcement {
    
    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="announce", type="string", nullable=false)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $announcement;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="updated_tstamp", type="datetimetz")
     */
    private $updatedTimestamp;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\AdminBundle\Entity\Announcement
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
     * @param string $announce
     * @return \CTF\AdminBundle\Entity\Announcement
     */
    public function setAnnouncement($announce) {
        $this->announcement = $announce;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getAnnouncement() {
        return $this->announcement;
    }
    
    /**
     * 
     * @param \DateTime $tstamp
     * @return \CTF\AdminBundle\Entity\Announcement
     */
    public function setUpdatedTimestamp($tstamp) {
        $this->updatedTimestamp = $tstamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getUpdatedTimestamp() {
        return $this->updatedTimestamp;
    }
}
