<?php


namespace Application\CrmBundle\Enum;


class WorkPermit
{
	const
		BORDER_COMMUTER_PERMIT = 1,
		EU_CITIZEN = 2,
		RESIDENCE_PERMIT = 3,
		SETTLING_PERMIT = 4,
		SHORT_STAY_PERMIT = 5,
		SWISS_CITIZEN = 6;

	public static function getChoices()
	{
		$constants = (new \ReflectionClass(__CLASS__))->getConstants();

		return array_map(function($v){ return str_replace('_',' ', $v); }, array_flip($constants));
	}
}