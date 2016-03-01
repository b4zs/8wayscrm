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

		$ganttItem = $this->createGanttItem();
		$ganttItem->addChild($this->createProjectFixerItem());
		$menu->addChild($ganttItem);
	}

	private function getKnpMenuFactory()
	{
		return $this->container->get('knp_menu.factory');
	}

	private function getRouter()
	{
		return $this->container->get('router');
	}

	protected function createGanttItem()
	{
		$createItem = $this->getKnpMenuFactory()->createItem('redmine.gantt', array(
			'uri' => $this->getRouter()->generate('application_redmine_integration_gantt'),
			'label' => 'Redmine gantt',
			'attributes' => array(
				'title' => 'Create member',
				'class' => ''
			)
		));
		$createItem->setExtra('icon', '');
		return $createItem;
	}

	protected function createProjectFixerItem()
	{
		$createItem = $this->getKnpMenuFactory()->createItem('redmine.gantt.fix_project', array(
			'uri' => $this->getRouter()->generate('application_redmine_integration_fix_project'),
			'label' => 'Redmine Project fixer',
			'attributes' => array(
				'title' => '',
				'class' => ''
			)
		));
		$createItem->setExtra('icon', '');
		return $createItem;
	}

}