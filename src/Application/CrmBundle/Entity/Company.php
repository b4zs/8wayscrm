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
     * @var string
     */
    private $mainContactInformation;

    /**
     * @var string
     */
    private $sectorOfActivity;

    /**
     * @var string
     */
    private $websites;

    /**
     * @var string
     */
    private $offices;

    /**
     * @var string
     */
    private $memberships;


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
     * Set websites
     *
     * @param string $websites
     * @return Company
     */
    public function setWebsites($websites)
    {
        $this->websites = $websites;

        return $this;
    }

    /**
     * Get websites
     *
     * @return string 
     */
    public function getWebsites()
    {
        return $this->websites;
    }

    /**
     * Set offices
     *
     * @param string $offices
     * @return Company
     */
    public function setOffices($offices)
    {
        $this->offices = $offices;

        return $this;
    }

    /**
     * Get offices
     *
     * @return string 
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * Set memberships
     *
     * @param string $memberships
     * @return Company
     */
    public function setMemberships($memberships)
    {
        $this->memberships = $memberships;

        return $this;
    }

    /**
     * Get memberships
     *
     * @return string 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }
}
