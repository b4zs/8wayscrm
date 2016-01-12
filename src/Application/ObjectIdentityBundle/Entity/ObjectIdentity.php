<?php

namespace Application\ObjectIdentityBundle\Entity;

use Application\CrmBundle\Entity\Client;
use Application\CrmBundle\Entity\Project;
use Octet\Ticketing\Bundle\Entity\NoteRelatedTrait;
use Octet\Ticketing\Bundle\Entity\ReminderRelatedTrait;

class ObjectIdentity
	extends \Core\ObjectIdentityBundle\Entity\ObjectIdentity
	implements
		\Octet\Ticketing\Lib\Model\NoteRelatedInterface,
		\Octet\Ticketing\Lib\Model\ReminderRelatedInterface
{
	use ReminderRelatedTrait;
	use NoteRelatedTrait;

	protected $id;

	/** @var  Project */
	protected $project;

	/** @var  Client */
	protected $abstractClient;

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
}