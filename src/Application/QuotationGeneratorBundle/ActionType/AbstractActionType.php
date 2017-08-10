<?php


namespace Application\QuotationGeneratorBundle\ActionType;


use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\QuestionAction;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractActionType
{
	/** @var  ContainerInterface */
	protected $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer($container)
	{
		$this->container = $container;
	}

	abstract public function execute(QuestionAction $action, FillOutAnswer $answer);
}