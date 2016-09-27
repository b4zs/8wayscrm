<?php

namespace Application\ObjectIdentityBundle\Entity;

use Application\CrmBundle\Entity\Client;
use Application\CrmBundle\Entity\CustomProperty;
use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Entity\Supplier;
use Application\UserBundle\Entity\User;

class ObjectIdentity
	extends \Core\ObjectIdentityBundle\Entity\ObjectIdentity
{

	protected $id;

	/** @var  Project */
	protected $project;

	/** @var  Client */
	protected $abstractClient;

	/** @var  Supplier */
	protected $supplier;

	/** @var  User */
	protected $user;

    /**
     * @var CustomProperty
     */
    protected $customProperty;

	public function getId()
	{
		return $this->id;
	}

	public function getProject()
	{
		return $this->project;
	}

	public function setProject(Project $project)
	{
		$this->project = $project;
	}

	public function getAbstractClient()
	{
		return $this->abstractClient;
	}

	public function setAbstractClient(Client $abstractClient)
	{
		$this->abstractClient = $abstractClient;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getSupplier()
	{
		return $this->supplier;
	}

	public function setSupplier(Supplier $supplier)
	{
		$this->supplier = $supplier;
	}

    /**
     * @return CustomProperty
     */
    public function getCustomProperty()
    {
        return $this->customProperty;
    }

    /**
     * @param CustomProperty $customProperty
     */
    public function setCustomProperty(CustomProperty $customProperty)
    {
        $this->customProperty = $customProperty;
    }

}