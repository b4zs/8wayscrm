<?php

namespace Application\AdminBundle\Menu;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\GenericEvent;

class SonataAdminMenuProvider extends ContainerAware implements MenuProviderInterface
{
	const MENU_NAME = 'sonata_admin_sidebar';
	/**
	 * @var MenuFactory
	 */
	private $knpMenuFactory;
	/**
	 * @var Pool
	 */
	private $adminPool;

	public function __construct(MenuFactory $knpMenuFactory, Pool $adminPool)
	{
		$this->knpMenuFactory = $knpMenuFactory;
		$this->adminPool = $adminPool;
	}

	function get($name, array $options = array())
	{
		if (self::MENU_NAME === $name) {
			return $this->buildSonataAdminMenu();
		}
	}

	function has($name, array $options = array())
	{
		return self::MENU_NAME === $name;
	}

	private function buildSonataAdminMenu(array $options = array())
	{
		$root = new MenuItem('', $this->knpMenuFactory);
		$sc = $this->container->get('security.context');
		$tr = $this->container->get('translator');
		$req= $this->container->get('request');
		$ed = $this->container->get('event_dispatcher');

		foreach ($this->adminPool->getDashboardGroups() as $groupName => $group) {
			if (in_array($groupName, array('sonata_notification'))) {
				continue;
			}
			$display = false;
			if (empty($group['roles']) || $sc->isGranted('ROLE_SUPER_ADMIN')) {
				$display = true;
			} else {
				$display = true;
				foreach ($group['roles'] as $role) {
					$display = $display && $sc->isGranted($group[$role]);
				}
			}

			if ($display) {
				$groupItem = $root->addChild($groupName);
				$groupItem->setLabel($tr->trans($group['label'], array(), $group['label_catalogue']));

				/** @var Admin $admin */
				foreach ($group['items'] as $admin) {
					if ($admin->hasroute('list') && $admin->isGranted('LIST')) {
						$adminItem = $groupItem->addChild($admin->getCode(), array(
							'uri' => $admin->generateUrl('list'),
						));
						$adminItem->setLabel($tr->trans($admin->getLabel(), array(), $admin->getTranslationDomain()));
						$active = $req->get('_sonata_admin') == $admin->getCode();
						if ($active) {
							$adminItem->setCurrent(true);
							$groupItem->setCurrent(true);
						}
					}
				}
			}
		}

		$ed->dispatch('menu.build.'. self::MENU_NAME, new GenericEvent($root));

		return $root;
	}


}