<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Enum\ProjectStatus;
use Application\CrmBundle\Model\OwnerGroupAware;
use Application\MediaBundle\Entity\Gallery;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Application\UserBundle\Entity\Group;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;

/**
 * Project
 */
class Project implements LogExtraDataAware, OwnerGroupAware
{
    use ObjectIdentityAwareTrait;

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
     * @var \Application\CrmBundle\Entity\AbstractClient
     */
    private $client;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var LogExtraData|null
     */
    private $logExtraData;

    /** @var  Gallery */
    private $fileset;

    /**
     * @var Group[]|Collection
     */
    private $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fileset = new Gallery();
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
     * @param \DateTime $createdAt
     * @return Project
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
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

    public function setClient(\Application\CrmBundle\Entity\AbstractClient $client = null)
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


    /**
     * @return LogExtraData|null
     */
    public function getLogExtraData()
    {
        return $this->logExtraData;
    }

    public function setLogExtraData(LogExtraData $logExtraData)
    {
        $this->logExtraData = $logExtraData;
    }

    public function setUpdatedAt(\DateTime $dateTime)
    {
        $this->updatedAt = $dateTime;
    }

    /**
     * @return Gallery
     */
    public function getFileset()
    {
        return $this->fileset;
    }

    /**
     * @param Gallery $fileset
     */
    public function setFileset($fileset)
    {
        $this->fileset = $fileset;
    }

    public function addGalleryHasMedias(GalleryHasMediaInterface $galleryHasMedia)
    {
        $this->getFileset()->addGalleryHasMedias($galleryHasMedia);
    }

    public function updateFilesetName(LifecycleEventArgs $eventArgs)
    {
        $this->getFileset()->setName($this->getName());
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroup(GroupInterface $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }
    }

    public function removeGroup(GroupInterface $group)
    {
        $this->groups->removeElement($group);
    }
}
