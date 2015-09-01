<?php

namespace Application\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyMembership
 */
class CompanyMembership
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $person;

    /**
     * @var string
     */
    private $role;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var string
     */
    private $endDate;

    /**
     * @var string
     */
    private $workPermit;

    /**
     * @var string
     */
    private $holidaysRemaining;


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
     * Set company
     *
     * @param string $company
     * @return CompanyMembership
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set person
     *
     * @param string $person
     * @return CompanyMembership
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return string 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return CompanyMembership
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return CompanyMembership
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
     * Set endDate
     *
     * @param string $endDate
     * @return CompanyMembership
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return string 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set workPermit
     *
     * @param string $workPermit
     * @return CompanyMembership
     */
    public function setWorkPermit($workPermit)
    {
        $this->workPermit = $workPermit;

        return $this;
    }

    /**
     * Get workPermit
     *
     * @return string 
     */
    public function getWorkPermit()
    {
        return $this->workPermit;
    }

    /**
     * Set holidaysRemaining
     *
     * @param string $holidaysRemaining
     * @return CompanyMembership
     */
    public function setHolidaysRemaining($holidaysRemaining)
    {
        $this->holidaysRemaining = $holidaysRemaining;

        return $this;
    }

    /**
     * Get holidaysRemaining
     *
     * @return string 
     */
    public function getHolidaysRemaining()
    {
        return $this->holidaysRemaining;
    }
}
