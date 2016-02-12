<?php


namespace Application\CrmBundle\Entity;


use Application\CrmBundle\Enum\ClientStatus;
use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityInterface;

class Client extends AbstractClient
{

	/**
	 * @var string
	 */
	private $status = ClientStatus::COLD;

	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getCanonicalName()
	{
		return $this->getCompany()->getName();
	}
}