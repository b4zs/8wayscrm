<?php

namespace Application\CrmBundle\Entity;

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
     * @var string
     */
    private $personalData;

    /**
     * @var string
     */
    private $contactInformation;

    /**
     * @var string
     */
    private $companyMemberships;


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
     * Set personalData
     *
     * @param string $personalData
     * @return Person
     */
    public function setPersonalData($personalData)
    {
        $this->personalData = $personalData;

        return $this;
    }

    /**
     * Get personalData
     *
     * @return string 
     */
    public function getPersonalData()
    {
        return $this->personalData;
    }

    /**
     * Set contactInformation
     *
     * @param string $contactInformation
     * @return Person
     */
    public function setContactInformation($contactInformation)
    {
        $this->contactInformation = $contactInformation;

        return $this;
    }

    /**
     * Get contactInformation
     *
     * @return string 
     */
    public function getContactInformation()
    {
        return $this->contactInformation;
    }

    /**
     * Set companyMemberships
     *
     * @param string $companyMemberships
     * @return Person
     */
    public function setCompanyMemberships($companyMemberships)
    {
        $this->companyMemberships = $companyMemberships;

        return $this;
    }

    /**
     * Get companyMemberships
     *
     * @return string 
     */
    public function getCompanyMemberships()
    {
        return $this->companyMemberships;
    }
}
