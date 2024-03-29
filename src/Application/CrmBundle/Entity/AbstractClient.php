<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Enum\ClientStatus;
use Application\CrmBundle\Model\OwnerGroupAware;
use Application\MediaBundle\Entity\Gallery;
use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Application\UserBundle\Entity\Group;
use Application\UserBundle\Entity\User;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Core\ObjectIdentityBundle\Model\IndexableObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;

class AbstractClient implements LogExtraDataAware, OwnerGroupAware, ObjectIdentityAware, IndexableObjectIdentityAware

{
    use ObjectIdentityAwareTrait;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var User
     */
    protected $owner;

    /**
     * @var User
     */
    protected $projectManager;

    /**
     * @var string
     */
    private $referral;

    /**
     * @var string
     */
    private $financialInformation;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $projects;

    /**
     * @var Company
     */
    protected $company;

    /**
     * @var Gallery
     */
    protected $fileset;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $contacts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $addresses;

    /**
     * @var \DateTime|null
     */
    private $deletedAt;


    /**
     * @var \DateTime|null
     */
    private $updatedAt;

    private $logExtraData;

    /**
     * @var Group[]|Collection
     */
    protected $groups;

    /**
     * @var ArrayCollection|CustomProperty[]
     */
    protected $customProperties;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->company          = new Company();
        $this->projects         = new ArrayCollection();
        $this->contacts         = new ArrayCollection();
        $this->addresses        = new ArrayCollection();
        $this->groups           = new ArrayCollection();
        $this->customProperties = new ArrayCollection();
        $this->createdAt        = new \DateTime();
        $this->fileset          = new Gallery();
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
     * Set type
     *
     * @param string $type
     * @return AbstractClient
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set financialInformation
     *
     * @param string $financialInformation
     * @return AbstractClient
     */
    public function setFinancialInformation($financialInformation)
    {
        $this->financialInformation = $financialInformation;

        return $this;
    }

    /**
     * Get financialInformation
     *
     * @return string 
     */
    public function getFinancialInformation()
    {
        return $this->financialInformation;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AbstractClient
     */
    public function setCreatedAt($createdAt)
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
     * Add projects
     *
     * @param \Application\CrmBundle\Entity\Project $projects
     * @return AbstractClient
     */
    public function addProject(\Application\CrmBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
        $projects->setClient($this);
        $this->setUpdatedAt(new \DateTime());

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Application\CrmBundle\Entity\Project $projects
     */
    public function removeProject(\Application\CrmBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
        $projects->setClient(null);
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set company
     *
     * @param \Application\CrmBundle\Entity\Company $company
     * @return AbstractClient
     */
    public function setCompany(\Application\CrmBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\CrmBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function addContact(\Application\CrmBundle\Entity\Contact $contact)
    {
        $this->contacts[] = $contact;
        $contact->setClient($this);

        return $this;
    }

    public function removeContact(\Application\CrmBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
        $contact->setClient(null);
    }

    /**
     * Get contactPersons
     *
     * @return \Doctrine\Common\Collections\Collection|Contact[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    public function getFirstContact() {
        if($this->contacts->count() > 0) {
            return $this->contacts->current();
        }

        return null;
    }

    function __toString()
    {
        return $this->getCompany()
//            ? ''
            ? (string)$this->getCompany()
            : ('#'.$this->getId())
        ;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return Individual
     */
    public function getIndividual()
    {
        return $this->individual;
    }

    /**
     * @param Individual $individual
     */
    public function setIndividual($individual)
    {
        $this->individual = $individual;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getLogExtraData()
    {
        return $this->logExtraData;
    }

    /**
     * @param mixed $logExtraData
     */
    public function setLogExtraData(LogExtraData $logExtraData)
    {
        $this->logExtraData = $logExtraData;
    }

    /**
     * @return User
     */
    public function getProjectManager()
    {
        return $this->projectManager;
    }

    /**
     * @param User $projectManager
     */
    public function setProjectManager($projectManager)
    {
        $this->projectManager = $projectManager;
    }

    /**
     * @return string
     */
    public function getReferral()
    {
        return $this->referral;
    }

    /**
     * @param string $referral
     */
    public function setReferral($referral)
    {
        $this->referral = $referral;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function addAddress(\Application\CrmBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;
        $address->setClient($this);

        return $this;
    }

    public function removeAddress(\Application\CrmBundle\Entity\Address $address)
    {
        $this->contacts->removeElement($address);
        $address->setClient(null);
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


    public function updateFilesetName(LifecycleEventArgs $eventArgs)
    {
        $this->getFileset()->setName($this->getCompany()->getName());
    }

    public function addGalleryHasMedias(GalleryHasMediaInterface $galleryHasMedia)
    {
        $this->getFileset()->addGalleryHasMedias($galleryHasMedia);
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


    /**
     * @return string
     */
    public function getCanonicalName()
    {
        return $this->getCompany()->getName();
    }

    public function getGalleryHasMedias() {
        return $this->fileset->getGalleryHasMedias();
    }

    public function removeGalleryHasMedia(GalleryHasMediaInterface $galleryHasMedia) {
        if($this->fileset->getGalleryHasMedias()->contains($galleryHasMedia)) {
            $this->fileset->getGalleryHasMedias()->removeElement($$galleryHasMedia);
        }
    }

    /**
     * @return CustomProperty[]|ArrayCollection
     */
    public function getCustomProperties()
    {
        return $this->customProperties;
    }

    /**
     * @param CustomProperty[]|ArrayCollection $customProperties
     */
    public function setCustomProperties($customProperties)
    {
        $this->customProperties = new ArrayCollection();

        foreach($customProperties as $customProperty) {
            $this->addCustomProperty($customProperty);
        }
    }

    public function addCustomProperty(CustomProperty $customProperty) {
        if(!$this->customProperties->contains($customProperty)) {
            $this->customProperties->add($customProperty);
            $customProperty->setClient($this);
        }
    }

    public function removeCustomProperty(CustomProperty $customProperty) {
        if($this->customProperties->contains($customProperty)) {
            $this->customProperties->removeElement($customProperty);
            $customProperty->setClient(null);
        }
    }
}
