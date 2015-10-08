<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Enum\ProjectStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 */
class Project
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
    private $createdAt;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $status = ProjectStatus::ASSESSMENT;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $memberships;

    /**
     * @var \Application\CrmBundle\Entity\Client
     */
    private $client;

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
     * Set name
     *
     * @param string $name
     * @return Project
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
     * Set createdAt
     *
     * @param string $createdAt
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Project
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
     * Set length
     *
     * @param string $length
     * @return Project
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return string 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Add memberships
     *
     * @param \Application\CrmBundle\Entity\ProjectMembership $memberships
     * @return Project
     */
    public function addMembership(\Application\CrmBundle\Entity\ProjectMembership $memberships)
    {
        $this->memberships[] = $memberships;
        $memberships->setProject($this);

        return $this;
    }

    /**
     * Remove memberships
     *
     * @param \Application\CrmBundle\Entity\ProjectMembership $memberships
     */
    public function removeMembership(\Application\CrmBundle\Entity\ProjectMembership $memberships)
    {
        $this->memberships->removeElement($memberships);
        $memberships->setProject(null);
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

    public function setClient(\Application\CrmBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    function __toString()
    {
        return $this->getClient() . ' - ' . $this->getName();
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
