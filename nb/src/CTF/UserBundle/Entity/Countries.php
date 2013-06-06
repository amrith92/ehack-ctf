<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Countries
 *
 * @ORM\Table(name="countries")
 * @ORM\Entity
 */
class Countries
{
    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="name", type="string", unique=true)
     */
    private $name;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="iso_code_2", type="string")
     */
    private $iso_code_2;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="iso_code_3", type="string")
     */
    private $iso_code_3;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="post_code_required", type="boolean")
     */
    private $post_code_required;
    
    /**
     * 
     * @param integer $id
     * @return \CTF\UserBundle\Entity\Countries
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
     * @param string $name
     * @return \CTF\UserBundle\Entity\Countries
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
    
    /**
     * 
     * @param string $code
     * @return \CTF\UserBundle\Entity\Countries
     */
    public function setIsoCode2($code)
    {
        $this->iso_code_2 = $code;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getIsoCode2()
    {
        return $this->iso_code_2;
    }
    
    /**
     * 
     * @param string $code
     * @return \CTF\UserBundle\Entity\Countries
     */
    public function setIsoCode3($code)
    {
        $this->iso_code_3 = $code;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getIsoCode3()
    {
        return $this->iso_code_3;
    }
    
    /**
     * 
     * @param boolean $req
     * @return \CTF\UserBundle\Entity\Countries
     */
    public function setPostCodeRequired($req)
    {
        $this->post_code_required = $req;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getPostCodeRequired()
    {
        return $this->post_code_required;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isPostCodeRequired()
    {
        return $this->post_code_required;
    }
}
