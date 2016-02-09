<?php

namespace Application\RedmineIntegrationBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\GenericEvent;

class MenuBuildListener extends ContainerAware
{
	public function buildSonataAdminMenu(GenericEvent $event)
	{
		if (!$this->container->get('security.token_storage')->getToken()->getUser()->getRedmineAuthToken()) {
			return;
		}
		$menu = $event->getSubject();
		$createItem = $this->getKnpMenuFactory()->createItem('sonata.user.admin.user.team.create', array(
			'uri'   => $this->getRouter()->generate('application_redmine_integration_gantt'),
			'label' => 'Redmine gantt',
			'attributes' => array(
				'title' => 'Create member',
				'class' => ''
			)
		));
		$createItem->setExtra('icon', '');
		$menu->addChild($createItem);

	}

	private function getKnpMenuFactory()
	{
		return $this->container->get('knp_menu.factory');
	}

	private function getRouter()
	{
		return $this->container->get('router');
	}

}