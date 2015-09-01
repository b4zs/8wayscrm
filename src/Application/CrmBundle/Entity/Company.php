<?php

namespace Application\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 */
class Company
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ContactInformation
     */
    private $mainContactInformation;

    /**
     * @var string
     */
    private $sectorOfActivity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $memberships;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $offices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $websites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->websites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mainContactInformation = new ContactInformation();
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
     * Set name
     *
     * @param string $name
     * @return Company
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
     * Set mainContactInformation
     *
     * @param string $mainContactInformation
     * @return Company
     */
    public function setMainContactInformation($mainContactInformation)
    {
        $this->mainContactInformation = $mainContactInformation;

        return $this;
    }

    /**
     * Get mainContactInformation
     *
     * @return string 
     */
    public function getMainContactInformation()
    {
        return $this->mainContactInformation;
    }

    /**
     * Set sectorOfActivity
     *
     * @param string $sectorOfActivity
     * @return Company
     */
    public function setSectorOfActivity($sectorOfActivity)
    {
        $this->sectorOfActivity = $sectorOfActivity;

        return $this;
    }

    /**
     * Get sectorOfActivity
     *
     * @return string 
     */
    public function getSectorOfActivity()
    {
        return $this->sectorOfActivity;
    }

    /**
     * Add memberships
     *
     * @param \Application\CrmBundle\Entity\CompanyMembership $memberships
     * @return Company
     */
    public function addMembership(\Application\CrmBundle\Entity\CompanyMembership $memberships)
    {
        $memberships->setCompany($this);
        $this->memberships[] = $memberships;

        return $this;
    }

    /**
     * Remove memberships
     *
     * @param \Application\CrmBundle\Entity\CompanyMembership $memberships
     */
    public function removeMembership(\Application\CrmBundle\Entity\CompanyMembership $memberships)
    {
        $this->memberships->removeElement($memberships);
        $memberships->setCompany(null);
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Add offices
     *
     * @param \Application\CrmBundle\Entity\Office $offices
     * @return Company
     */
    public function addOffice(\Application\CrmBundle\Entity\Office $offices)
    {
        $this->offices[] = $offices;
        $offices->setCompany($this);

        return $this;
    }

    /**
     * Remove offices
     *
     * @param \Application\CrmBundle\Entity\Office $offices
     */
    public function removeOffice(\Application\CrmBundle\Entity\Office $offices)
    {
        $this->offices->removeElement($offices);
        $offices->setCompany(null);
    }

    /**
     * Get offices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * Add websites
     *
     * @param \Application\CrmBundle\Entity\Website $websites
     * @return Company
     */
    public function addWebsite(\Application\CrmBundle\Entity\Website $websites)
    {
        $this->websites[] = $websites;

        return $this;
    }

    /**
     * Remove websites
     *
     * @param \Application\CrmBundle\Entity\Website $websites
     */
    public function removeWebsite(\Application\CrmBundle\Entity\Website $websites)
    {
        $this->websites->removeElement($websites);
    }

    /**
     * Get websites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWebsites()
    {
        return $this->websites;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getName();
    }


}
