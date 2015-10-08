<?php

namespace Application\CrmBundle\Menu;

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

//		$this->modifyContactManagerMenu($menu);
		$this->modifyProjectsMenu($menu);
//		$this->modifyLeadsMenu($menu);


		$this->removeSystemTables($menu);
		$this->removeMediaLibrary($menu);
//		$this->removeUsers($menu);

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

	protected function modifyContactManagerMenu(MenuItem $menu)
	{

		$statuses = ProjectStatus::getChoices();
		$mainMenuItem = $menu->getChild('Contact Manager');


		foreach ($mainMenuItem->getChildren() as $adminCode => $child) {
			$icons = array(
				'application_crm.admin.team_member'     => 'group',
				'application_crm.admin.contact_person'  => 'phone',
				'application_crm.admin.individual'      => 'male',
				'application_crm.admin.company'         => 'building',
			);

			if (isset($icons[$adminCode])) {
				$child->setExtra('icon', 'fa fa-'.$icons[$adminCode]);
			}

			if (in_array($adminCode, array('application_crm.admin.team_member'))) {
				$admin = $this->container->get($adminCode);
				$this->createdMenuItems[$adminCode.'#create'] = $createItem = $this->getKnpMenuFactory()->createItem('Create '.$child->getLabel(), array(
					'uri'   => $admin->generateUrl('create'),
				));
				$createItem->setExtra('icon', 'fa fa-plus');
				$child->addChild($createItem);
			}

		}
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
		$statuses = ClientStatus::getAllData();
		$adminCode = 'application_crm.admin.lead';
		$mainMenuItem = $menu->getChild('Leads');

		$mainAdminItem = $mainMenuItem->getChild($adminCode);
		$mainAdminUri = $mainAdminItem->getUri();
		$mainMenuItem->removeChild($adminCode);
		$mainMenuItem->setUri($mainAdminUri);

		/** @var ClientAdmin $admin */
		$admin = $this->container->get($adminCode);

//		$addLeadItem = $mainMenuItem->addChild('Add');
//		$addLeadItem->setUri($admin->generateUrl('create'));
//		$addLeadItem->setExtra('icon', 'fa fa-user-plus');

		$createItemAdded = false;
		foreach ($statuses as $status => $statusData) {
			$this->createdMenuItems[$adminCode.'#'.$status] = $statusItem = $this->getKnpMenuFactory()->createItem($statusData['title'], array(
				'uri' => $admin->generateUrl(
					'list',
					array('filter' => array('status' => array('type' => '', 'value' => $status,)))
				),
			));

			if (!$createItemAdded) {
				$addLeadItem = $statusItem->addChild('Create lead');
				$addLeadItem->setUri($admin->generateUrl('create'));
				$addLeadItem->setExtra('icon', 'fa fa-plus');
				$createItemAdded = true;
			}

			if (!empty($statusData['icon'])) {
				$statusItem->setExtra('icon', 'fa fa-'.$statusData['icon']);
			}

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

	private function removeMediaLibrary(MenuItem $menu)
	{
		$menu->removeChild('sonata_media');
	}

	private function removeUsers(MenuItem $menu)
	{
		$menu->removeChild('sonata_user');
	}

	private function removeSystemTables($menu)
	{
		$menu->removeChild('system_tables');
	}


}