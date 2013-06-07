<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GlobalState
 *
 * @ORM\Table(name="global_state")
 * @ORM\Entity
 */
class GlobalState
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_ctf", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $enableCtf;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_chat", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $enableChat;


}
