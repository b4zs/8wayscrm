<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Enum\LeadStatus;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Doctrine\ORM\Mapping as ORM;

class Lead implements LogExtraDataAware
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $type = 'company';

    /**
     * @var string
     */
    private $financialInformation;

    /**
     * @var string
     */
    private $status = LeadStatus::PROSPECT;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $projects;

    /**
     * @var \Application\CrmBundle\Entity\Company
     */
    private $company;

    /**
     * @var \Application\CrmBundle\Entity\Individual
     */
    private $individual;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactPersons;

    /**
     * @var Person
     */
    private $owner;

    /**
     * @var \DateTime|null
     */
    private $deletedAt;


    /**
     * @var \DateTime|null
     */
    private $updatedAt;

    private $logExtraData;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->company = new Company();
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contactPersons = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return Lead
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set financialInformation
     *
     * @param string $financialInformation
     * @return Lead
     */
    public function setFinancialInformation($financialInformation)
    {
        $this->financialInformation = $financialInformation;

        return $this;
    }

    /**
     * Get financialInformation
     *
     * @return string 
     */
    public function getFinancialInformation()
    {
        return $this->financialInformation;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Lead
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Lead
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
     * Add projects
     *
     * @param \Application\CrmBundle\Entity\Project $projects
     * @return Lead
     */
    public function addProject(\Application\CrmBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
        $projects->setLead($this);
        $this->setUpdatedAt(new \DateTime());

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Application\CrmBundle\Entity\Project $projects
     */
    public function removeProject(\Application\CrmBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
        $projects->setLead(null);
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set company
     *
     * @param \Application\CrmBundle\Entity\Company $company
     * @return Lead
     */
    public function setCompany(\Application\CrmBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\CrmBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Add contactPersons
     *
     * @param \Application\CrmBundle\Entity\Person $contactPersons
     * @return Lead
     */
    public function addContactPerson(\Application\CrmBundle\Entity\Person $contactPersons)
    {
        $this->contactPersons[] = $contactPersons;

        return $this;
    }

    /**
     * Remove contactPersons
     *
     * @param \Application\CrmBundle\Entity\Person $contactPersons
     */
    public function removeContactPerson(\Application\CrmBundle\Entity\Person $contactPersons)
    {
        $this->contactPersons->removeElement($contactPersons);
    }

    /**
     * Get contactPersons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactPersons()
    {
        return $this->contactPersons;
    }

    function __toString()
    {
        return $this->getCompany() ? (string)$this->getCompany() : ('#'.$this->getId());
    }

    /**
     * @return Person
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Person $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return Individual
     */
    public function getIndividual()
    {
        return $this->individual;
    }

    /**
     * @param Individual $individual
     */
    public function setIndividual($individual)
    {
        $this->individual = $individual;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getLogExtraData()
    {
        return $this->logExtraData;
    }

    /**
     * @param mixed $logExtraData
     */
    public function setLogExtraData(LogExtraData $logExtraData)
    {
        $this->logExtraData = $logExtraData;
    }


}
