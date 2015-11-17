<?php

namespace Application\CrmBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 */
class Person
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var \DateTime
     */
    private $dateOfBirth;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $nationality;

    /**
     * @var string
     */
    private $directLinePhone;

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
     * @var string
     */
    private $twitter;

    /**
     * @var string
     */
    private $instagram;

   

    function __toString()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Person
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return Person
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set companyPhone
     *
     * @param string $companyPhone
     *
     * @return Person
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
     *
     * @return Person
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
     *
     * @return Person
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
     *
     * @return Person
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
     *
     * @return Person
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
     *
     * @return Person
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
     * Set twitter
     *
     * @param string $twitter
     *
     * @return Person
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set instagram
     *
     * @param string $instagram
     *
     * @return Person
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get instagram
     *
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @return string
     */
    public function getDirectLinePhone()
    {
        return $this->directLinePhone;
    }

    /**
     * @param string $directLinePhone
     */
    public function setDirectLinePhone($directLinePhone)
    {
        $this->directLinePhone = $directLinePhone;
    }

    public function getFullName()
    {
        $name = array_filter(array($this->getFirstName(), $this->getLastName()));
        return implode(' ', $name);
    }

    public function setFullName($name)
    {
        $name = explode(' ', $name);
        $this->setFirstName(array_shift($name));
        $this->setLastName(implode(' ', $name));
    }
}
