<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users
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
     * @ORM\Column(name="fname", type="string", length=50, nullable=false)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="lname", type="string", length=50, nullable=false)
     */
    private $lname;

    /**
     * @var string
     *
     * @ORM\Column(name="screen_name", type="string", length=30, nullable=true)
     */
    private $screenName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="blob", nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="dp", type="blob", nullable=true)
     */
    private $dp;

    /**
     * @var string
     *
     * @ORM\Column(name="about_me", type="text", nullable=true)
     */
    private $aboutMe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="date", nullable=true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=14, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=25, nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=30, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="text", nullable=true)
     */
    private $website;

    /**
     * @var point
     *
     * @ORM\Column(name="location", type="point", nullable=true)
     */
    private $location;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sms_verify", type="boolean", nullable=true)
     */
    private $smsVerify;

    /**
     * @var string
     *
     * @ORM\Column(name="login_mode", type="string", nullable=true)
     */
    private $loginMode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_banned", type="boolean", nullable=true)
     */
    private $isBanned;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_tstamp", type="datetime", nullable=false)
     */
    private $updatedTstamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_tstamp", type="datetime", nullable=false)
     */
    private $createdTstamp;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="user")
     */
    private $team;

    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->team = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fname
     *
     * @param string $fname
     * @return Users
     */
    public function setFname($fname)
    {
        $this->fname = $fname;
    
        return $this;
    }

    /**
     * Get fname
     *
     * @return string 
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname
     *
     * @param string $lname
     * @return Users
     */
    public function setLname($lname)
    {
        $this->lname = $lname;
    
        return $this;
    }

    /**
     * Get lname
     *
     * @return string 
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set screenName
     *
     * @param string $screenName
     * @return Users
     */
    public function setScreenName($screenName)
    {
        $this->screenName = $screenName;
    
        return $this;
    }

    /**
     * Get screenName
     *
     * @return string 
     */
    public function getScreenName()
    {
        return $this->screenName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set dp
     *
     * @param string $dp
     * @return Users
     */
    public function setDp($dp)
    {
        $this->dp = $dp;
    
        return $this;
    }

    /**
     * Get dp
     *
     * @return string 
     */
    public function getDp()
    {
        return $this->dp;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return Users
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;
    
        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string 
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     * @return Users
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    
        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime 
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Users
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Users
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Users
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Users
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Users
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set location
     *
     * @param point $location
     * @return Users
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return point 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set smsVerify
     *
     * @param boolean $smsVerify
     * @return Users
     */
    public function setSmsVerify($smsVerify)
    {
        $this->smsVerify = $smsVerify;
    
        return $this;
    }

    /**
     * Get smsVerify
     *
     * @return boolean 
     */
    public function getSmsVerify()
    {
        return $this->smsVerify;
    }

    /**
     * Set loginMode
     *
     * @param string $loginMode
     * @return Users
     */
    public function setLoginMode($loginMode)
    {
        $this->loginMode = $loginMode;
    
        return $this;
    }

    /**
     * Get loginMode
     *
     * @return string 
     */
    public function getLoginMode()
    {
        return $this->loginMode;
    }

    /**
     * Set isBanned
     *
     * @param boolean $isBanned
     * @return Users
     */
    public function setIsBanned($isBanned)
    {
        $this->isBanned = $isBanned;
    
        return $this;
    }

    /**
     * Get isBanned
     *
     * @return boolean 
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * Set updatedTstamp
     *
     * @param \DateTime $updatedTstamp
     * @return Users
     */
    public function setUpdatedTstamp($updatedTstamp)
    {
        $this->updatedTstamp = $updatedTstamp;
    
        return $this;
    }

    /**
     * Get updatedTstamp
     *
     * @return \DateTime 
     */
    public function getUpdatedTstamp()
    {
        return $this->updatedTstamp;
    }

    /**
     * Set createdTstamp
     *
     * @param \DateTime $createdTstamp
     * @return Users
     */
    public function setCreatedTstamp($createdTstamp)
    {
        $this->createdTstamp = $createdTstamp;
    
        return $this;
    }

    /**
     * Get createdTstamp
     *
     * @return \DateTime 
     */
    public function getCreatedTstamp()
    {
        return $this->createdTstamp;
    }

    /**
     * Add team
     *
     * @param \CTF\CommonBundle\Entity\Team $team
     * @return Users
     */
    public function addTeam(\CTF\CommonBundle\Entity\Team $team)
    {
        $this->team[] = $team;
    
        return $this;
    }

    /**
     * Remove team
     *
     * @param \CTF\CommonBundle\Entity\Team $team
     */
    public function removeTeam(\CTF\CommonBundle\Entity\Team $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get team
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set org
     *
     * @param \CTF\CommonBundle\Entity\Organization $org
     * @return Users
     */
    public function setOrg(\CTF\CommonBundle\Entity\Organization $org = null)
    {
        $this->org = $org;
    
        return $this;
    }

    /**
     * Get org
     *
     * @return \CTF\CommonBundle\Entity\Organization 
     */
    public function getOrg()
    {
        return $this->org;
    }
}