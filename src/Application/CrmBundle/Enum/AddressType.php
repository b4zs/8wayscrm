<?php


namespace Application\CrmBundle\Enum;


class AddressType
{
	const OFFICE = 'office';
	const BILLING = 'billing';

	public static function getChoices()
	{
		return array(
			self::OFFICE => 'office',
			self::BILLING => 'billing',
		);
	}

}