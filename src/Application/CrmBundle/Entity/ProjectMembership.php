<?php

namespace Application\CrmBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectMembership
 */
class ProjectMembership
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $role;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \Application\CrmBundle\Entity\Project
     */
    private $project;

    /**
     * @var User
     */
    private $user;

    function __construct()
    {
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
     * Set role
     *
     * @param string $role
     * @return ProjectMembership
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProjectMembership
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
     * Set project
     *
     * @param \Application\CrmBundle\Entity\Project $project
     * @return ProjectMembership
     */
    public function setProject(\Application\CrmBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


}
