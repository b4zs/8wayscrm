<?php


namespace Application\CrmBundle\Enum;


class ProjectStatus
{

	const ASSESSMENT    = 'assessment';
	const QUOTATION     = 'quotation';
	const PREPARATION   = 'Preparation';
	const EXECUTION     = 'Execution';
	const DELIVERED     = 'Delivered';
	const SLEEPING      = 'Sleeping';
	const CANCELLED     = 'Cancelled';

	public static function getChoices()
	{
		$ref = new \ReflectionClass(__CLASS__);
		$statuses = $ref->getConstants();

		return array_combine($statuses, array_map('ucfirst', $statuses));
	}

	public static function getAllData()
	{
		return array(
			self::ASSESSMENT    => array('label' => self::ASSESSMENT   ,'icon' => 'fa fa-question-circle'),
			self::QUOTATION     => array('label' => self::QUOTATION    ,'icon' => 'fa fa-calculator'),
			self::PREPARATION   => array('label' => self::PREPARATION  ,'icon' => 'fa fa-spinner'),
			self::EXECUTION     => array('label' => self::EXECUTION    ,'icon' => 'fa fa-gears'),
			self::DELIVERED     => array('label' => self::DELIVERED    ,'icon' => 'fa fa-check-circle'),
			self::SLEEPING      => array('label' => self::SLEEPING     ,'icon' => 'fa fa-clock-o'),
			self::CANCELLED     => array('label' => self::CANCELLED    ,'icon' => 'fa fa-remove'),
		);
	}

}