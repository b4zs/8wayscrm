<?php

namespace Application\ProjectAccountingBundle\Entity;

use Application\RedmineIntegrationBundle\Entity\RedmineSpentTime;
use Application\RedmineIntegrationBundle\Entity\RedmineTicket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpentTime
 *
 * @ORM\Table(name="accounting__spent_time")
 * @ORM\Entity(repositoryClass="Application\ProjectAccountingBundle\Entity\SpentTimeRepository")
 */
class SpentTime
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Application\CrmBundle\Entity\Project")
     */
    private $project;

    /**
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="Application\ProjectAccountingBundle\Entity\Work")
     */
    private $work;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var RedmineTicket[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\RedmineIntegrationBundle\Entity\RedmineTicket")
     * @ORM\JoinTable(name="accounting__spent_time_redmine_ticket")
     */
    private $redmineTickets;

    /**
     * @var RedmineSpentTime
     *
     * @ORM\ManyToOne(targetEntity="Application\RedmineIntegrationBundle\Entity\RedmineSpentTime")
     */
    private $redmineTimeEntry;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="float", nullable=true)
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->redmineTickets = new ArrayCollection();
        $this->startDate = new \DateTime();
        $this->duration = 0.1;
        $this->createdAt = new \DateTime();
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
     * Set project
     *
     * @param integer $project
     *
     * @return SpentTime
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return integer
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set work
     *
     * @param integer $work
     *
     * @return SpentTime
     */
    public function setWork($work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * Get work
     *
     * @return integer
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return SpentTime
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set redmineTickets
     *
     * @param integer $redmineTickets
     *
     * @return SpentTime
     */
    public function setRedmineTickets($redmineTickets)
    {
        $this->redmineTickets = $redmineTickets;

        return $this;
    }

    /**
     * Get redmineTickets
     *
     * @return integer
     */
    public function getRedmineTickets()
    {
        return $this->redmineTickets;
    }

    /**
     * Set redmineTimeEntry
     *
     * @param integer $redmineTimeEntry
     *
     * @return SpentTime
     */
    public function setRedmineTimeEntry($redmineTimeEntry)
    {
        $this->redmineTimeEntry = $redmineTimeEntry;

        return $this;
    }

    /**
     * Get redmineTimeEntry
     *
     * @return integer
     */
    public function getRedmineTimeEntry()
    {
        return $this->redmineTimeEntry;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return SpentTime
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return SpentTime
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SpentTime
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

