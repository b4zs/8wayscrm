<?php

namespace Application\CrmBundle\Entity;

use Application\CrmBundle\Model\OwnerGroupAware;
use Application\UserBundle\Entity\Group;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;

/**
 * CompanyMembership
 */
class Contact implements OwnerGroupAware
{
    /**
     * @var integer
     */
    private $id;


    /** @var  string */
    private $title;

    /**
     * @var string
     */
    private $role;


    /**
     * @var \Application\CrmBundle\Entity\AbstractClient
     */
    private $client;

    /**
     * @var \Application\CrmBundle\Entity\Person
     */
    private $person;

    /**
     * @var string
     */
    private $note;

    /**
     * @var Group[]|Collection
     */
    private $groups;

    function __construct()
    {
        $this->person = new Person();
        $this->groups = new ArrayCollection();
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
     * @return AbstractClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param AbstractClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Set person
     *
     * @param \Application\CrmBundle\Entity\Person $person
     * @return CompanyMembership
     */
    public function setPerson(\Application\CrmBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Application\CrmBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getClient() && $this->getPerson()
            ? sprintf('%s - %s', $this->getClient(), $this->getPerson())
            : 'new';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
