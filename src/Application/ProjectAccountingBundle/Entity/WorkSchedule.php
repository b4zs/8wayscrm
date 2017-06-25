<?php

namespace Application\ProjectAccountingBundle\Entity;

use Application\RedmineIntegrationBundle\Entity\RedmineTicket;
use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkSchedule
 *
 * @ORM\Table(name="accounting__work_schedule")
 * @ORM\Entity(repositoryClass="Application\ProjectAccountingBundle\Entity\WorkScheduleRepository")
 */
class WorkSchedule
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
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="Application\ProjectAccountingBundle\Entity\Work")
     */
    private $work;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scheduleDate", type="datetime", nullable=true)
     */
    private $scheduleDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="scheduleDuration", type="float", nullable=true)
     */
    private $scheduleDuration;

    /**
     * @var RedmineTicket[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\RedmineIntegrationBundle\Entity\RedmineTicket")
     * @ORM\JoinTable(name="accounting__work_schedule_redmine_ticket")
     */
    private $redmineTickets;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User")
     */
    private $createdBy;

    public function __construct()
    {
        $this->redmineTickets = new ArrayCollection();
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
     * Set work
     *
     * @param integer $work
     *
     * @return WorkSchedule
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
     * Set name
     *
     * @param string $name
     *
     * @return WorkSchedule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WorkSchedule
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

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return WorkSchedule
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
     * Set scheduleDate
     *
     * @param \DateTime $scheduleDate
     *
     * @return WorkSchedule
     */
    public function setScheduleDate($scheduleDate)
    {
        $this->scheduleDate = $scheduleDate;

        return $this;
    }

    /**
     * Get scheduleDate
     *
     * @return \DateTime
     */
    public function getScheduleDate()
    {
        return $this->scheduleDate;
    }

    /**
     * Set scheduleDuration
     *
     * @param integer $scheduleDuration
     *
     * @return WorkSchedule
     */
    public function setScheduleDuration($scheduleDuration)
    {
        $this->scheduleDuration = $scheduleDuration;

        return $this;
    }

    /**
     * Get scheduleDuration
     *
     * @return integer
     */
    public function getScheduleDuration()
    {
        return $this->scheduleDuration;
    }

    /**
     * Set redmineTickets
     *
     * @param integer $redmineTickets
     *
     * @return WorkSchedule
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return WorkSchedule
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return WorkSchedule
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    function __toString()
    {
        $parts = array(
            ($this->getUser() ? '@'.$this->getUser() : null),
            ($this->getName() ? $this->getName() : null),
            ($this->getWork() ? $this->getWork() : null),
        );

        return implode(' - ', array_filter($parts));
    }


}

