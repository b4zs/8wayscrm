<?php


namespace Application\QuotationGeneratorBundle\Enum;


class RequiredUserRole
{
	const ROLE_USER = 'ROLE_USER';
	const ROLE_SALES = 'ROLE_SALES';
	const ROLE_PROJECT_MANAGER = 'ROLE_PROJECT_MANAGER';

	public static function getChoices()
	{
		return array(
			self::ROLE_USER => 'ROLE_USER',
		);
	}

}