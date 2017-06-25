<?php

namespace Application\ProjectAccountingBundle\Entity;

use Application\MediaBundle\Entity\Gallery;
use Application\ProjectAccountingBundle\Enum\WorkNature;
use Application\ProjectAccountingBundle\Enum\WorkStatus;
use Application\ProjectAccountingBundle\Enum\WorkTracker;
use Application\RedmineIntegrationBundle\Entity\RedmineTicket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Work
 *
 * @ORM\Table(name="accounting__work")
 * @ORM\Entity(repositoryClass="Application\ProjectAccountingBundle\Entity\WorkRepository")
 */
class Work
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
     * @var Invoice[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\ProjectAccountingBundle\Entity\Invoice")
     */
    private $invoices;

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
     * @var integer
     *
     * @ORM\Column(name="tracker", type="integer", nullable=true)
     */
    private $tracker;

    /**
     * @var integer
     *
     * @ORM\Column(name="nature", type="integer", nullable=true)
     */
    private $nature;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="initialEstimatedTime", type="integer", nullable=true)
     */
    private $initialEstimatedTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="currentlyEstimatedTime", type="integer", nullable=true)
     */
    private $currentlyEstimatedTime;

    /**
     * @var Price
     *
     * @ORM\Embedded(class="Application\ProjectAccountingBundle\Entity\Price")
     */
    private $hourlyRate;

    /**
     * @var SpentTime[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\ProjectAccountingBundle\Entity\SpentTime", mappedBy="work_id")
     */
    private $spentTimes;

    /**
     * @var WorkSchedule[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\ProjectAccountingBundle\Entity\WorkSchedule", mappedBy="work_id")
     */
    private $workSchedules;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var RedmineTicket[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\RedmineIntegrationBundle\Entity\RedmineTicket")
     * @ORM\JoinTable(name="accounting__work_redmine_ticket")
     */
    private $redmineTickets;

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="Application\MediaBundle\Entity\Gallery", cascade={"all"})
     */
    private $fileSet;

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
        $this->nature = WorkNature::PAID;
        $this->status = WorkStatus::QUOTED;
        $this->tracker = WorkTracker::MIXED;

        $this->hourlyRate = new Price();
        $this->invoices = new ArrayCollection();
        $this->workSchedules = new ArrayCollection();
        $this->spentTimes = new ArrayCollection();
        $this->redmineTickets = new ArrayCollection();
        $this->fileSet = new Gallery();
        $this->fileSet->setName('Work files');
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
     * @return Work
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
     * @return Work
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;

        return $this;
    }

    /**
     * Get invoices
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Work
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
     * @return Work
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
     * Set tracker
     *
     * @param integer $tracker
     *
     * @return Work
     */
    public function setTracker($tracker)
    {
        $this->tracker = $tracker;

        return $this;
    }

    /**
     * Get tracker
     *
     * @return integer
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * Set nature
     *
     * @param integer $nature
     *
     * @return Work
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return integer
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Work
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set initialEstimatedTime
     *
     * @param integer $initialEstimatedTime
     *
     * @return Work
     */
    public function setInitialEstimatedTime($initialEstimatedTime)
    {
        $this->initialEstimatedTime = $initialEstimatedTime;

        return $this;
    }

    /**
     * Get initialEstimatedTime
     *
     * @return integer
     */
    public function getInitialEstimatedTime()
    {
        return $this->initialEstimatedTime;
    }

    /**
     * Set currentlyEstimatedTime
     *
     * @param integer $currentlyEstimatedTime
     *
     * @return Work
     */
    public function setCurrentlyEstimatedTime($currentlyEstimatedTime)
    {
        $this->currentlyEstimatedTime = $currentlyEstimatedTime;

        return $this;
    }

    /**
     * Get currentlyEstimatedTime
     *
     * @return integer
     */
    public function getCurrentlyEstimatedTime()
    {
        return $this->currentlyEstimatedTime;
    }

    /**
     * Set hourlyRate
     *
     * @param integer $hourlyRate
     *
     * @return Work
     */
    public function setHourlyRate($hourlyRate)
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    /**
     * Get hourlyRate
     *
     * @return integer
     */
    public function getHourlyRate()
    {
        return $this->hourlyRate;
    }

    /**
     * Set spentTimes
     *
     * @param integer $spentTimes
     *
     * @return Work
     */
    public function setSpentTimes($spentTimes)
    {
        $this->spentTimes = $spentTimes;

        return $this;
    }

    /**
     * Get spentTimes
     *
     * @return integer
     */
    public function getSpentTimes()
    {
        return $this->spentTimes;
    }

    /**
     * Set workSchedules
     *
     * @param integer $workSchedules
     *
     * @return Work
     */
    public function setWorkSchedules($workSchedules)
    {
        $this->workSchedules = $workSchedules;

        return $this;
    }

    /**
     * Get workSchedules
     *
     * @return integer
     */
    public function getWorkSchedules()
    {
        return $this->workSchedules;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Work
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set redmineTickets
     *
     * @param integer $redmineTickets
     *
     * @return Work
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
     * Set files
     *
     * @param integer $files
     *
     * @return Work
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return integer
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Work
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
     * @return Work
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
        $trackerChoices = WorkTracker::getChoices();
        $parts = array(
            ($this->getProject() ? $this->getProject() : null),
            ($this->getName() ? $this->getName() : null),
            ($this->getTracker() ? $trackerChoices[$this->getTracker()] : null),
            ($this->getCreatedAt() ? $this->getCreatedAt()->format('Y-m-d') : null),
        );
        return implode(' / ', array_filter($parts));
    }


}

