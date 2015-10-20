<?php

namespace Core\LoggableEntityBundle\Block;

use Core\LoggableEntityBundle\Admin\Extension\LoggableEntityExtension;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityLogBlockService extends BaseBlockService
{
	/** @var ContainerInterface */
	private $container;

	/** @var  LoggableEntityExtension */
	private $loggableAdminExtension;

	public function execute(BlockContextInterface $blockContext, Response $response = null)
	{
		$className = $blockContext->getSetting('subject_class');
		$id = $blockContext->getSetting('subject_id');

		$entries =  $this
			->loggableAdminExtension
			->buildLogEntriesQueryForEntity($className, $id)
			->getResult();


		return $this->renderResponse($blockContext->getTemplate(), array(
			'block_context'  => $blockContext,
			'block'          => $blockContext->getBlock(),
			'entries'        => $entries,
		), $response);
	}


	public function setDefaultSettings(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'template'      => 'CoreLoggableEntityBundle:Block:entityLogBlock.html.twig',
			'subject_class' => null,
			'subject_id'    => null,
		));
	}

	public function setLoggableAdminExtension($loggableAdminExtension)
	{
		$this->loggableAdminExtension = $loggableAdminExtension;
	}


}