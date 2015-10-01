<?php


namespace Application\CrmBundle\Enum;


class LeadType
{


	const CLIENT = 'client';

	const SUPPLIER = 'supplier';

	public static function getChoices()
	{
		$types = array(
			self::CLIENT, self::SUPPLIER,
		);

		return array_combine($types, array_map('ucfirst', $types));
	}
}