<?php

namespace Application\CrmBundle\EventListener;

use Application\CrmBundle\Admin\ClientAdmin;
use Application\CrmBundle\Admin\ProjectAdmin;
use Application\CrmBundle\Enum\ClientStatus;
use Application\CrmBundle\Enum\ProjectStatus;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class AdminMenuBuildListener extends ContainerAware
{
	private $createdMenuItems = array();

	public function onAdminMenuBuild(GenericEvent $event)
	{

		/** @var MenuItem $menu */
		$menu = $event->getSubject();


		$this->modifyProjectsMenu($menu);
		$this->modifyLeadsMenu($menu);

		$this->markActiveMenuItem();
	}

	/**
	 * @return \Knp\Menu\MenuFactory
	 */
	protected function getKnpMenuFactory()
	{
		$menuFactory = $this->container->get('knp_menu.factory');
		return $menuFactory;
	}

	protected function modifyProjectsMenu(MenuItem $menu)
	{
		$statuses = ProjectStatus::getChoices();
		$adminCode = 'application_crm.admin.project';
		$mainMenuItem = $menu->getChild('Projects');

		$mainMenuItem->removeChild($adminCode);
		/** @var ProjectAdmin $admin */
		$admin = $this->container->get($adminCode);

		foreach ($statuses as $status) {
			$this->createdMenuItems[$adminCode.'#'.$status] = $statusItem = $this->getKnpMenuFactory()->createItem($status, array(
				'uri' => $admin->generateUrl(
					'list',
					array('filter' => array('status' => array('type' => '', 'value' => $status,)))
				),
			));

			$mainMenuItem->addChild($statusItem);
		}
	}

	protected function modifyLeadsMenu(MenuItem $menu)
	{
		$statuses = ClientStatus::getChoices();
		$adminCode = 'application_crm.admin.client';
		$mainMenuItem = $menu->getChild('Leads');

		$mainMenuItem->removeChild($adminCode);
		/** @var ClientAdmin $admin */
		$admin = $this->container->get($adminCode);

		foreach ($statuses as $status) {
			$this->createdMenuItems[$adminCode.'#'.$status] = $statusItem = $this->getKnpMenuFactory()->createItem($status, array(
				'uri' => $admin->generateUrl(
					'list',
					array('filter' => array('status' => array('type' => '', 'value' => $status,)))
				),
			));

			$mainMenuItem->addChild($statusItem);
		}
	}

	protected function markActiveMenuItem()
	{
		$request = $this->container->get('request');
		$activeAdminCode = $request->get('_sonata_admin');
		if ($activeAdminCode) {
			$activeAdmin = $this->container->get('sonata.admin.pool')->getAdminByAdminCode($activeAdminCode);
			$activeAdmin->setRequest($request);
			$filterParameters = $activeAdmin->getFilterParameters();
			$pa = new PropertyAccessor();
			$filterStatus = $pa->getValue($filterParameters, '[status][value]');
			$subjectStatus = null;

			if ($subject = $activeAdmin->getSubject()) {
				$subjectStatus = method_exists($subject, 'getStatus') ? $subject->getStatus() : null;
			}

			$status = $subjectStatus ? $subjectStatus : $filterStatus;

			/** @var MenuItem $activeItem */
			if ($status && $activeItem = $this->createdMenuItems[$activeAdminCode . '#' . $status]) {
				$activeItem->setCurrent(true);
			}
		}
	}


}