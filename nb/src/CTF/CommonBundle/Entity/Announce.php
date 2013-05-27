<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Announce
 *
 * @ORM\Table(name="announce")
 * @ORM\Entity
 */
class Announce
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
     * @ORM\Column(name="announce", type="text", nullable=false)
     */
    private $announce;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_tstamp", type="datetime", nullable=false)
     */
    private $updatedTstamp;



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
     * Set announce
     *
     * @param string $announce
     * @return Announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
    
        return $this;
    }

    /**
     * Get announce
     *
     * @return string 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set updatedTstamp
     *
     * @param \DateTime $updatedTstamp
     * @return Announce
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
}