<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StagesLevels
 *
 * @ORM\Table(name="stages_levels")
 * @ORM\Entity
 */
class StagesLevels
{
    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var \Questions
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Questions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * @var \Stages
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Stages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stage_id", referencedColumnName="id")
     * })
     */
    private $stage;


}
