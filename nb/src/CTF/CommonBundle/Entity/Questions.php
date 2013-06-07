<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questions
 *
 * @ORM\Table(name="questions")
 * @ORM\Entity
 */
class Questions
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
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_tpl", type="text", nullable=false)
     */
    private $answerTpl;

    /**
     * @var string
     *
     * @ORM\Column(name="hints", type="text", nullable=false)
     */
    private $hints;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="UserQuest", mappedBy="question")
     */
    private $quest;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->quest = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}
