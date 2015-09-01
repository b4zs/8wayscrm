<?php

namespace Application\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 */
class Client
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $type = 'new';

    /**
     * @var string
     */
    private $financialInformation;

    /**
     * @var string
     */
    private $status;

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
     * @var \Application\CrmBundle\Entity\Person
     */
    private $account;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactPersons;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * @return Client
     */
    public function addProject(\Application\CrmBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
        $projects->setClient($this);

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
        $projects->setClient(null);
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
     * @return Client
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
     * Set account
     *
     * @param \Application\CrmBundle\Entity\Person $account
     * @return Client
     */
    public function setAccount(\Application\CrmBundle\Entity\Person $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Application\CrmBundle\Entity\Person 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Add contactPersons
     *
     * @param \Application\CrmBundle\Entity\Person $contactPersons
     * @return Client
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


}
