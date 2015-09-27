<?php

namespace Application\CrmBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    private $websites;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $leads;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->websites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mainContactInformation = new ContactInformation();
        $this->leads = new \Doctrine\Common\Collections\ArrayCollection();
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

    function __toString()
    {
        return $this->getName() ? $this->getName() : 'new';
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

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeads()
    {
        return $this->leads;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $leads
     */
    public function setLeads($leads)
    {
        $this->leads = $leads;
    }

    public function addLead(\Application\CrmBundle\Entity\Lead $lead)
    {
        $lead->setCompany($this);
        $this->websites[] = $lead;

        return $this;
    }

    public function removeLead(\Application\CrmBundle\Entity\Lead $lead)
    {
        $lead->setCompany(null);
        $this->websites->removeElement($lead);
    }
}
