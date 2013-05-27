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


}
