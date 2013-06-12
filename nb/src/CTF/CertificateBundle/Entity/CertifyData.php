<?php

namespace CTF\CertificateBundle\Entity;

class CertifyData {
    
    /**
     * @var string
     */
    private $fullname;
    
    /**
     * @var string
     */
    private $team;
    
    /**
     * @var string
     */
    private $organization;
    
    /**
     * @var integer
     */
    private $score;
    
    /**
     * @var integer
     */
    private $rank;
    
    /**
     * @var \DateTime
     */
    private $timestamp;
    
    /**
     * @var string
     */
    private $serial;
    
    /**
     * 
     * @param string $name
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setFullName($name) {
        $this->fullname = $name;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getFullName() {
        return $this->fullname;
    }
    
    /**
     * 
     * @param string $team
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setTeam($team) {
        $this->team = $team;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getTeam() {
        return $this->team;
    }
    
    /**
     * 
     * @param string $org
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setOrganization($org) {
        $this->organization = $org;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getOrganization() {
        return $this->organization;
    }
    
    /**
     * 
     * @param integer $score
     * @return \CTF\AdminBundle\Entity\CertifyData
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
     * @param integer $rank
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setRank($rank) {
        $this->rank = $rank;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getRank() {
        return $this->rank;
    }
    
    /**
     * 
     * @param \DateTime $tstamp
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setTimestamp($tstamp) {
        $this->timestamp = $tstamp;
        
        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    /**
     * 
     * @param string $serial
     * @return \CTF\AdminBundle\Entity\CertifyData
     */
    public function setSerial($serial) {
        $this->serial = $serial;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getSerial() {
        return $this->serial;
    }
}
