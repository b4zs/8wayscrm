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

	const CONTACT_MANAGER = 'Contact Manager';

	public function onAdminMenuBuild(GenericEvent $event)
	{
		/** @var MenuItem $menu */
		$menu = $event->getSubject();

		$this->addTeamMenu($menu);
		$this->modifyClientsMenu($menu);
		$this->modifySuppliersMenu($menu);

		$this->reorderContactManager($menu);

		$this->modifyProjectsMenu($menu);


		$this->removeSystemTables($menu);
		$this->removeMediaLibrary($menu);

		if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
			$this->removeUsers($menu);
		}

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

		$mainMenuItem = $menu->getChild(self::CONTACT_MANAGER);


		foreach ($mainMenuItem->getChildren() as $adminCode => $child) {
			$icons = array(
				'application_crm.admin.team_member'     => 'group',
//				'application_crm.admin.contact_person'  => 'phone',
				'application_crm.admin.individual'      => 'male',
				'application_crm.admin.company'         => 'building',
			);

			if (isset($icons[$adminCode])) {
				$child->setExtra('icon', 'fa fa-'.$icons[$adminCode]);
			}
		}
	}
	protected function modifyProjectsMenu(MenuItem $menu)
	{
		$statuses = ProjectStatus::getAllData();
		$adminCode = 'application_crm.admin.project';
		$this->createdMenuItems[$adminCode] = $mainMenuItem = $menu->getChild('Projects');

		$mainMenuItem->removeChild($adminCode);
		/** @var ProjectAdmin $admin */
		$admin = $this->container->get($adminCode);
		$mainMenuItem->setUri($admin->generateUrl('list'));

		foreach ($statuses as $status => $statusData) {
			$this->createdMenuItems[$adminCode.'#'.$status] = $statusItem = $this->getKnpMenuFactory()->createItem($status, array(
				'uri' => $admin->generateUrl(
					'list',
					array('filter' => array('status' => array('type' => '', 'value' => $status,)))
				),
			));
			$statusItem->setExtra('icon', $statusData['icon']);

			$mainMenuItem->addChild($statusItem);
		}
	}

	protected function modifyClientsMenu(MenuItem $menu)
	{
		$statuses = ClientStatus::getAllData();
		$adminCode = 'application_crm.admin.client';

		$mainAdminItem = $menu->getChild(self::CONTACT_MANAGER)->getChild($adminCode);
		$mainAdminItem->setExtra('icon', 'fa fa-building');

		/** @var ClientAdmin $admin */
		$admin = $this->container->get($adminCode);

		$createItemAdded = false;
		foreach ($statuses as $status => $statusData) {
			$this->createdMenuItems[$adminCode.'#'.$status] = $statusItem = $this->getKnpMenuFactory()->createItem($statusData['title'], array(
				'uri' => $admin->generateUrl(
					'list',
					array('filter' => array('status' => array('type' => '', 'value' => $status,)))
				),
			));
			$mainAdminItem->addChild($statusItem);

			if (!$createItemAdded) {
				$this->createdMenuItems[$adminCode.'#create'] = $addLeadItem = $mainAdminItem->addChild($adminCode.'.create');
				$addLeadItem->setUri($admin->generateUrl('create'));
				$addLeadItem->setExtra('icon', 'fa fa-plus');
				$addLeadItem->setLabel('');
				$addLeadItem->setAttributes(array(
					'title' => 'Create lead',
					'class' => 'pull-right sidemenu-pull-right'
				));
				$createItemAdded = true;
			}

			if (!empty($statusData['icon'])) {
				$statusItem->setExtra('icon', 'fa fa-'.$statusData['icon']);
			}

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

			if (!$status
				&& $request->attributes->get('_controller')
				&& preg_match('/\:\:([a-zA-Z0-9]+)Action/', $request->attributes->get('_controller'), $out)) {
				$action = $out[1];
				if ($action && 'list' !== $action) {
					$status = $action;
				}
			}

			$index = $activeAdminCode . ($status ? '#'.$status : '');

			/** @var MenuItem $activeItem */
			if ($status && isset($this->createdMenuItems[$index]) && $activeItem = $this->createdMenuItems[$index]) {
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

	private function removeSystemTables(MenuItem $menu)
	{
		$menu->removeChild('system_tables');
	}

	private function addTeamMenu(MenuItem $menu)
	{
		$adminCode = 'sonata.user.admin.user';
		$userItem = $menu->getChild('sonata_user')->getChild($adminCode);
		if ($userItem) {
			$contactManagerItem = $menu->getChild(self::CONTACT_MANAGER);
			$teamItem = clone $userItem;
			$teamItem->setName($adminCode.'.team');
			$teamItem->setLabel('Team');
			$teamItem->setParent(null);
			$teamItem->setExtra('icon', 'fa fa-user');
			$this->createdMenuItems[$adminCode] = $contactManagerItem->addChild($teamItem);

			$admin = $this->container->get($adminCode);
			$this->createdMenuItems[$adminCode.'#create'] = $createItem = $this->getKnpMenuFactory()->createItem('sonata.user.admin.user.team.create', array(
				'uri'   => $admin->generateUrl('create'),
				'label' => '',
				'attributes' => array(
					'title' => 'Create member',
					'class' => 'pull-right sidemenu-pull-right'
				)
			));
			$createItem->setExtra('icon', 'fa fa-plus  fa-fw');
			$contactManagerItem->addChild($createItem);
		}
	}

	private function reorderContactManager(MenuItem $menu)
	{
		$contactManagerItem = $menu->getChild(self::CONTACT_MANAGER);
		$contactManagerItem->reorderChildren(array(
			'sonata.user.admin.user.team',
			'sonata.user.admin.user.team.create',
			'application_crm.admin.client',
			'application_crm.admin.supplier',
		));
	}

	private function isGranted($role)
	{
		return $this->container->get('security.authorization_checker')->isGranted($role);
	}

	private function modifySuppliersMenu($menu)
	{
		$adminCode = 'application_crm.admin.supplier';

		$mainAdminItem = $menu->getChild(self::CONTACT_MANAGER)->getChild($adminCode);
		$mainAdminItem->setExtra('icon', 'fa fa-support');

	}


}