<?php


namespace Application\CrmBundle\Enum;


class PersonType
{
	const TEAM_MEMBER = 'team_member';
	const INDIVIDUAL = 'invidvidual';
	const COMPANY_CONTACT = 'company_contact';

	public static function getChoices()
	{
		return array(
			self::TEAM_MEMBER       => 'team member',
			self::INDIVIDUAL        => 'individual',
			self::COMPANY_CONTACT   => 'company contact',
		);
	}
}