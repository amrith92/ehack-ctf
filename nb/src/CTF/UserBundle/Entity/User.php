<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * CTF\UserBundle\User\Entity
 * 
 * @ORM\Entity(repositoryClass="CTF\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="auth_users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="google_id", type="string", length=50, nullable=false, unique=true)
     */
    protected $google_id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="google_access_token", type="string", length=255)
     */
    protected $google_access_token;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=false, unique=true)
     */
    protected $facebook_id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="facebook_access_token", type="string", length=255)
     */
    protected $facebook_access_token;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="twitter_id", type="string", length=50, nullable=false, unique=true)
     */
    protected $twitter_id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="twitter_access_token", type="string", length=255)
     */
    protected $twitter_access_token;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fname", type="string", length=50, nullable=false)
     */
    protected $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="lname", type="string", length=50, nullable=false)
     */
    protected $lname;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="date", nullable=false)
     */
    private $dob;
    
    /**
     * @var string
     *
     * @ORM\Column(name="about_me", type="text", nullable=true)
     */
    private $aboutMe;
    
    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", nullable=false)
     */
    private $gender;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="login_mode", type="string", nullable=true)
     */
    private $loginMode;
    
    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * Set googleId
     * 
     * @param string $id
     * @return User
     */
    public function setGoogleId($id)
    {
        $this->google_id = $id;
        
        return $this;
    }
    
    /**
     * Get googleId
     * 
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }
    
    /**
     * Set googleAccessToken
     * 
     * @param string $token
     * @return User
     */
    public function setGoogleAccessToken($token)
    {
        $this->google_access_token = $token;
        
        return $this;
    }
    
    /**
     * Get googleAccessToken
     * 
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }
    
    /**
     * Set facebookId
     * 
     * @param string $id
     * @return User
     */
    public function setFacebookId($id)
    {
        $this->facebook_id = $id;
        
        return $this;
    }
    
    /**
     * Get facebookId
     * 
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }
    
    /**
     * Set facebookAccessToken
     * 
     * @param string $token
     * @return User
     */
    public function setFacebookAccessToken($token)
    {
        $this->facebook_access_token = $token;
        
        return $this;
    }
    
    /**
     * Get facebookAccessToken
     * 
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }
    
    /**
     * Set twitterId
     * 
     * @param string $id
     * @return User
     */
    public function setTwitterId($id)
    {
        $this->twitter_id = $id;
        
        return $this;
    }
    
    /**
     * Get twitterId
     * 
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitter_id;
    }
    
    /**
     * Set twitterAccessToken
     * 
     * @param string $token
     * @return User
     */
    public function setTwitterAccessToken($token)
    {
        $this->twitter_access_token = $token;
        
        return $this;
    }
    
    /**
     * Get twitterAccessToken
     * 
     * @return string
     */
    public function getTwitterAccessToken()
    {
        return $this->twitter_access_token;
    }
    
    /**
     * Set fname
     *
     * @param string $fname
     * @return User
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
     * @return User
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
     * Set dp
     *
     * @param string $dp
     * @return User
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
     * Set dob
     *
     * @param \DateTime $dob
     * @return User
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
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return User
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
     * Set gender
     *
     * @param string $gender
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Set loginMode
     *
     * @param string $loginMode
     * @return User
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
     * Set org
     *
     * @param \CTF\CommonBundle\Entity\Organization $org
     * @return User
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