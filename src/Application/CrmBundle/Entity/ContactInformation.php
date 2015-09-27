<?php

namespace Application\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactInformation
 */
class ContactInformation
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $companyPhone;

    /**
     * @var string
     */
    private $privatePhone;

    /**
     * @var string
     */
    private $companyEmail;

    /**
     * @var string
     */
    private $privateEmail;

    /**
     * @var string
     */
    private $skypeId;

    /**
     * @var string
     */
    private $facebookId;

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
     * Set companyPhone
     *
     * @param string $companyPhone
     * @return ContactInformation
     */
    public function setCompanyPhone($companyPhone)
    {
        $this->companyPhone = $companyPhone;

        return $this;
    }

    /**
     * Get companyPhone
     *
     * @return string 
     */
    public function getCompanyPhone()
    {
        return $this->companyPhone;
    }

    /**
     * Set privatePhone
     *
     * @param string $privatePhone
     * @return ContactInformation
     */
    public function setPrivatePhone($privatePhone)
    {
        $this->privatePhone = $privatePhone;

        return $this;
    }

    /**
     * Get privatePhone
     *
     * @return string 
     */
    public function getPrivatePhone()
    {
        return $this->privatePhone;
    }

    /**
     * Set companyEmail
     *
     * @param string $companyEmail
     * @return ContactInformation
     */
    public function setCompanyEmail($companyEmail)
    {
        $this->companyEmail = $companyEmail;

        return $this;
    }

    /**
     * Get companyEmail
     *
     * @return string 
     */
    public function getCompanyEmail()
    {
        return $this->companyEmail;
    }

    /**
     * Set privateEmail
     *
     * @param string $privateEmail
     * @return ContactInformation
     */
    public function setPrivateEmail($privateEmail)
    {
        $this->privateEmail = $privateEmail;

        return $this;
    }

    /**
     * Get privateEmail
     *
     * @return string 
     */
    public function getPrivateEmail()
    {
        return $this->privateEmail;
    }

    /**
     * Set skypeId
     *
     * @param string $skypeId
     * @return ContactInformation
     */
    public function setSkypeId($skypeId)
    {
        $this->skypeId = $skypeId;

        return $this;
    }

    /**
     * Get skypeId
     *
     * @return string 
     */
    public function getSkypeId()
    {
        return $this->skypeId;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return ContactInformation
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
