<?php


namespace Application\QuotationGeneratorBundle\Enum;


class Stage
{
	const
		BEFORE_CONTRACT = 1,
		AFTER_CONTRACT = 2;

	static $apiToIntMap = array(
		'STAGE_BEFORE_CONTRACT' => self::BEFORE_CONTRACT,
		'STAGE_AFTER_CONTRACT' => self::AFTER_CONTRACT
	);

	public static function getChoices()
	{
		return array(
			self::BEFORE_CONTRACT => 'Before contract',
			self::AFTER_CONTRACT => 'After contract',
		);
	}

	public static function getApiChoices()
	{
		return array(
			'STAGE_BEFORE_CONTRACT' => 'Before contract',
			'STAGE_AFTER_CONTRACT' => 'After contract',
		);
	}

	public static function mapApiChoiceToInteger($apiValue)
	{
		return isset(self::$apiToIntMap[$apiValue]) ? self::$apiToIntMap[$apiValue] : null;
	}
	public static function mapIntegerToApiChoice($int)
	{
		$ix = array_search($int, self::$apiToIntMap);

		return false === $ix ? null : $ix;
	}
}