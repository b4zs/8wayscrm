<?php


namespace Application\CrmBundle\Entity;


use Application\CrmBundle\Enum\ClientStatus;

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
}