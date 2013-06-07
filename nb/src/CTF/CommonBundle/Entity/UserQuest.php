<?php

namespace CTF\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserQuest
 *
 * @ORM\Table(name="user_quest")
 * @ORM\Entity
 */
class UserQuest
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
     * @var integer
     *
     * @ORM\Column(name="quest_level", type="integer", nullable=false)
     */
    private $questLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_level", type="integer", nullable=false)
     */
    private $currentLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Questions", inversedBy="quest")
     * @ORM\JoinTable(name="user_quest_history",
     *   joinColumns={
     *     @ORM\JoinColumn(name="quest_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     *   }
     * )
     */
    private $question;

    /**
     * @var \AuthUsers
     *
     * @ORM\ManyToOne(targetEntity="AuthUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Stages
     *
     * @ORM\ManyToOne(targetEntity="Stages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="current_stage", referencedColumnName="id")
     * })
     */
    private $currentStage;

    /**
     * @var \Stages
     *
     * @ORM\ManyToOne(targetEntity="Stages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quest_stage", referencedColumnName="id")
     * })
     */
    private $questStage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->question = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}
