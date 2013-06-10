<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Zone
 *
 * @ORM\Table(name="zone")
 * @ORM\Entity(repositoryClass="CTF\UserBundle\Entity\ZoneRepository")
 */
class Zone
{
    /**
     * @var integer
     *
     * @ORM\Column(name="zone_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var CTF\UserBundle\Entity\Countries
     * 
     * @ORM\ManyToOne(targetEntity="Countries")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id")
     */
    private $country;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="code", type="text")
     */
    private $code;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="name", type="text", length=200)
     */
    private $name;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\UserBundle\Entity\Zone
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @param \CTF\UserBundle\Entity\Countries $country
     * @return \CTF\UserBundle\Entity\Zone
     */
    public function setCountry(\CTF\UserBundle\Entity\Countries $country)
    {
        $this->country = $country;
        
        return $this;
    }
    
    /**
     * 
     * @return CTF\UserBundle\Entity\Countries
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * 
     * @param string $code
     * @return \CTF\UserBundle\Entity\Zone
     */
    public function setCode($code)
    {
        $this->code = $code;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * 
     * @param string $name
     * @return \CTF\UserBundle\Entity\Zone
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
