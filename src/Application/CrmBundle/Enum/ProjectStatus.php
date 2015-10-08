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

}