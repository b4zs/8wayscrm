<?php

namespace Application\CrmBundle\EventListener;

use Application\CrmBundle\Model\OwnerGroupAware;
use Application\CrmBundle\Security\CrmSecurityHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\Event\LifecycleEventArgs;


class OwnerGroupSetterListener
{
	/** @var CrmSecurityHelper */
	private $securityHelper;

	public function __construct(CrmSecurityHelper $securityHelper)
	{
		$this->securityHelper = $securityHelper;
	}

	public function prePersist(LifecycleEventArgs $eventArgs)
	{
		$object = $eventArgs->getObject();

		if ($object instanceof OwnerGroupAware && null !== ($group = $this->securityHelper->fetchGroupOfCurrentUser()) && !$this->securityHelper->isGranted('ROLE_SUPER_ADMIN')) {
			if ($object->getGroups()->count() < 1) {
				$object->addGroup($group);
			}
		}
	}

}