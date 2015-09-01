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
     * @var \Application\CrmBundle\Entity\Company
     */
    private $company;

    /**
     * @var \Application\CrmBundle\Entity\Person
     */
    private $person;


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

    /**
     * Set company
     *
     * @param \Application\CrmBundle\Entity\Company $company
     * @return CompanyMembership
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
     * Set person
     *
     * @param \Application\CrmBundle\Entity\Person $person
     * @return CompanyMembership
     */
    public function setPerson(\Application\CrmBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Application\CrmBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getCompany() && $this->getPerson()
            ? sprintf('%s - %s', $this->getCompany(), $this->getPerson())
            : 'new';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


}
