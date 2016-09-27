<?php

namespace Application\CrmBundle\EventListener;

use Application\CrmBundle\Model\OwnerGroupAware;
use Application\CrmBundle\Security\CrmSecurityHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class OwnerGroupSetterListener implements ContainerAwareInterface 
{
	use ContainerAwareTrait;

	public function prePersist(LifecycleEventArgs $eventArgs)
	{
		$object = $eventArgs->getObject();

		if ($object instanceof OwnerGroupAware && null !== ($group = $this->getSecurityHelper()->fetchGroupOfCurrentUser()) && !$this->getSecurityHelper()->isGranted('ROLE_SUPER_ADMIN')) {
			if ($object->getGroups()->count() < 1) {
				$object->addGroup($group);
			}
		}
	}

	protected function getSecurityHelper() {
	    return $this->container->get('application_crm.security.helper');
    }

}