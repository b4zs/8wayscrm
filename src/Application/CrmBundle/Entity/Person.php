<?php

namespace Application\CrmBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 */
class Person
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $companyMemberships;

    /**
     * @var \Application\CrmBundle\Entity\PersonalData
     */
    private $personalData;

    /**
     * @var \Application\CrmBundle\Entity\ContactInformation
     */
    private $contactInformation;

    /**
     * @var Collection
     */
    private $projectMemberships;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->personalData = new PersonalData();
        $this->contactInformation = new ContactInformation();
        $this->companyMemberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projectMemberships = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add companyMemberships
     *
     * @param \Application\CrmBundle\Entity\CompanyMembership $companyMemberships
     * @return Person
     */
    public function addCompanyMembership(\Application\CrmBundle\Entity\CompanyMembership $companyMemberships)
    {
        $this->companyMemberships[] = $companyMemberships;

        return $this;
    }

    /**
     * Remove companyMemberships
     *
     * @param \Application\CrmBundle\Entity\CompanyMembership $companyMemberships
     */
    public function removeCompanyMembership(\Application\CrmBundle\Entity\CompanyMembership $companyMemberships)
    {
        $this->companyMemberships->removeElement($companyMemberships);
    }

    /**
     * Get companyMemberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanyMemberships()
    {
        return $this->companyMemberships;
    }

    /**
     * Set personalData
     *
     * @param \Application\CrmBundle\Entity\PersonalData $personalData
     * @return Person
     */
    public function setPersonalData(\Application\CrmBundle\Entity\PersonalData $personalData = null)
    {
        $this->personalData = $personalData;

        return $this;
    }

    /**
     * Get personalData
     *
     * @return \Application\CrmBundle\Entity\PersonalData 
     */
    public function getPersonalData()
    {
        return $this->personalData;
    }

    /**
     * Set contactInformation
     *
     * @param \Application\CrmBundle\Entity\ContactInformation $contactInformation
     * @return Person
     */
    public function setContactInformation(\Application\CrmBundle\Entity\ContactInformation $contactInformation = null)
    {
        $this->contactInformation = $contactInformation;

        return $this;
    }

    /**
     * Get contactInformation
     *
     * @return \Application\CrmBundle\Entity\ContactInformation 
     */
    public function getContactInformation()
    {
        return $this->contactInformation;
    }

    function __toString()
    {
        return $this->getPersonalData()->__toString();
    }

}
