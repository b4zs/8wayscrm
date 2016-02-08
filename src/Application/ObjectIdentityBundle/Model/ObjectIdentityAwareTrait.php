<?php


namespace Application\ObjectIdentityBundle\Model;


use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityInterface;

trait ObjectIdentityAwareTrait
{
	/** @var  ObjectIdentity */
	private $objectIdentity;

	protected function initObjectIdentity()
	{
		$this->objectIdentity = new ObjectIdentity($this);
	}


	/** @return ObjectIdentity|\Core\ObjectIdentityBundle\Entity\ObjectIdentity */
	public function getObjectIdentity()
	{
		return $this->objectIdentity;
	}

	public function setObjectIdentity(ObjectIdentityInterface $objectIdentity)
	{
		$this->objectIdentity = $objectIdentity;
	}



}