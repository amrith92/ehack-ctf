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
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;


}
