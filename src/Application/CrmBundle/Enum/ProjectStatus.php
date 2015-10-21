<?php


namespace Application\CrmBundle\Enum;


class ProjectStatus
{

	const ASSESSMENT    = 'assessment';
	const QUOTATION     = 'quotation';
	const PREPARATION   = 'preparation';
	const EXECUTION     = 'execution';
	const DELIVERED     = 'delivered';
	const SLEEPING      = 'sleeping';
	const CANCELLED     = 'cancelled';

	public static function getChoices()
	{
		$ref = new \ReflectionClass(__CLASS__);
		$statuses = $ref->getConstants();

		return array_combine($statuses, array_map('ucfirst', $statuses));
	}

	public static function getAllData()
	{
		return array(
			self::ASSESSMENT    => array('label' => ucfirst(self::ASSESSMENT)   ,'icon' => 'fa fa-question-circle'),
			self::QUOTATION     => array('label' => ucfirst(self::QUOTATION)    ,'icon' => 'fa fa-calculator'),
			self::PREPARATION   => array('label' => ucfirst(self::PREPARATION)  ,'icon' => 'fa fa-spinner'),
			self::EXECUTION     => array('label' => ucfirst(self::EXECUTION)    ,'icon' => 'fa fa-gears'),
			self::DELIVERED     => array('label' => ucfirst(self::DELIVERED)    ,'icon' => 'fa fa-check-circle'),
			self::SLEEPING      => array('label' => ucfirst(self::SLEEPING)     ,'icon' => 'fa fa-clock-o'),
			self::CANCELLED     => array('label' => ucfirst(self::CANCELLED)    ,'icon' => 'fa fa-remove'),
		);
	}

}