<?php

namespace CTF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * \CTF\UserBundle\User\Entity
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
     *
     * @var boolean
     * 
     * @ORM\Column(name="verified", type="boolean", nullable=false)
     */
    protected $verified;
    
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
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\MaxLength(limit=50, groups={"registration"})
     */
    protected $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="lname", type="string", length=50, nullable=false)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\MaxLength(limit=50, groups={"registration"})
     */
    protected $lname;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="dp", type="string")
     */
    protected $imageUrl;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="thumbnail", type="string")
     */
    protected $thumbnail;
    
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
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\MaxLength(limit=30, groups={"registration"})
     */
    private $phone;
    
    /**
     *
     * @var \CTF\UserBundle\Entity\Countries
     * 
     * @ORM\ManyToOne(targetEntity="\CTF\UserBundle\Entity\Countries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="country_id")
     * })
     */
    private $country;

    /**
     * @var \CTF\UserBundle\Entity\Zone
     *
     * @ORM\ManyToOne(targetEntity="\CTF\UserBundle\Entity\Zone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="zone_id")
     * })
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
     * Assert\NotBlank(groups={"registration"})
     */
    private $location;
    
    /**
     * @var string
     *
     * @ORM\Column(name="login_mode", type="string", nullable=true)
     */
    private $loginMode;
    
    /**
     * @var \CTF\UserBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="\CTF\UserBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(groups={"registration"})
     */
    private $org;
    
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="\CTF\UserBundle\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="ref_invites",
     *      joinColumns={@ORM\JoinColumn(name="referrer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="invitee_id", referencedColumnName="id")}
     * )
     */
    private $invitations;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * 
     * @param boolean $verified
     * @return \CTF\UserBundle\Entity\User
     */
    public function setVerified($verified) {
        $this->verified = $verified;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getVerified() {
        return $this->verified;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isVerified() {
        return $this->verified;
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
     * @param string $url
     * @return User
     */
    public function setImageUrl($url)
    {
        $this->imageUrl = $url;
    
        return $this;
    }

    /**
     * Get Image URL
     *
     * @return string 
     */
    public function getImageURL()
    {
        return $this->imageUrl;
    }
    
    /**
     * Sets thumbnail
     * 
     * @param string $url
     * @return \CTF\UserBundle\Entity\User
     */
    public function setThumbnail($url)
    {
        $this->thumbnail = $url;
        
        return $this;
    }
    
    /**
     * Get thumbnail
     * 
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
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
     * Set country
     *
     * @param \CTF\UserBundle\Entity\Countries $country
     * @return User
     */
    public function setCountry(\CTF\UserBundle\Entity\Countries $country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \CTF\UserBundle\Entity\Countries
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set state
     *
     * @param \CTF\UserBundle\Entity\Zone $state
     * @return User
     */
    public function setState(\CTF\UserBundle\Entity\Zone $state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return \CTF\UserBundle\Entity\Zone
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
     * @param \CTF\UserBundle\Entity\Organization $org
     * @return User
     */
    public function setOrg(\CTF\UserBundle\Entity\Organization $org = null)
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
    
    /**
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $invites
     * @return \CTF\UserBundle\Entity\User
     */
    public function setInvitations($invites) {
        $this->invitations = $invites;
        
        return $this;
    }
    
    /**
     * 
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getInvitations() {
        return $this->invitations;
    }
    
    /**
     * 
     * @param \CTF\UserBundle\Entity\User $invite
     * @return \CTF\UserBundle\Entity\User
     */
    public function addInvite($invite) {
        $this->invitations[] = $invite;
        
        return $this;
    }
}