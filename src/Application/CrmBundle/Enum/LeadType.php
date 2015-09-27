<?php


namespace Application\CrmBundle\Enum;


class LeadType
{


	const COMPANY = 'company';

	const INDIVIDUAL = 'individual';

	const SUPPLIER = 'supplier';

	public static function getChoices()
	{
		$types = array(
			self::COMPANY, self::INDIVIDUAL, self::SUPPLIER,
		);

		return array_combine($types, array_map('ucfirst', $types));
	}
}