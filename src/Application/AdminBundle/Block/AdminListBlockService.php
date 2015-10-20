<?php

namespace Application\AdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminListBlockService extends \Sonata\AdminBundle\Block\AdminListBlockService
{
	/**
	 * {@inheritdoc}
	 */
	public function execute(BlockContextInterface $blockContext, Response $response = null)
	{
		$dashboardGroups = $this->pool->getDashboardGroups();

		$settings = $blockContext->getSettings();
		$settings['groups'] = array('Contact Manager', 'Projects');

		$visibleGroups = array();
		foreach ($dashboardGroups as $name => $dashboardGroup) {
			if (!$settings['groups'] || in_array($name, $settings['groups'])) {
				$visibleGroups[] = $dashboardGroup;
			}
		}

		return $this->renderPrivateResponse($this->pool->getTemplate('list_block'), array(
			'block'         => $blockContext->getBlock(),
			'settings'      => $settings,
			'admin_pool'    => $this->pool,
			'groups'        => $visibleGroups,
		), $response);
	}


}