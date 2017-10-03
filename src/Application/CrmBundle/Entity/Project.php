<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Enum\ProjectStatus;
use Application\CrmBundle\Model\OwnerGroupAware;
use Application\MediaBundle\Entity\Gallery;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Application\UserBundle\Entity\Group;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Model\GroupInterface;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;

/**
 * Project
 */
class Project implements LogExtraDataAware, OwnerGroupAware, ObjectIdentityAware
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

    /**
     * @var Gallery
     */
    private $fileset;

    /**
     * @var Group[]|Collection
     */
    private $groups;

    /**
     * @var ArrayCollection
     */
    private $children;

    /**
     * @var integer
     */
    private $lft;

    /**
     * @var integer
     */
    private $lvl;

    /**
     * @var integer
     */
    private $rgt;

    /**
     * @var integer
     */
    private $root;

    /**
     * @var Project
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memberships = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->fileset = new Gallery();
        $this->createdAt = new \DateTime();
        $this->initObjectIdentity();
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
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
        return $this->getName().($this->getClient() ? ' ('.$this->getClient().')' : '');
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

    public function getCanonicalName()
    {
        return $this->getName();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if($this->getLft() + 1 !== $this->getRgt()){
            return true;
        }

        return false;
    }

    /**
     * @param bool $maxLength
     * @return ArrayCollection
     */
    public function getChildren($maxLength = true)
    {
        //@TODO load once only one level
        if ($this->getLvl() > 0 && $maxLength == true) {
            return new ArrayCollection();
        }

        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     * @return $this
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     * @return $this
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
        return $this;
    }

    /**
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     * @return $this
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param mixed $rgt
     * @return $this
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getClientName()
    {
        if ($this->getClient()) {
            return $this->getClient()->getCanonicalName();
        }

        return '';
    }

    public function getClientId()
    {
        if ($this->getClient()) {
            return $this->getClient()->getId();
        }

        return '';
    }
}
