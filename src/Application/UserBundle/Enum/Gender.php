<?php

namespace Application\UserBundle\Enum;


class Gender
{
	const
		MALE = 'm',
		FEMALE = 'f';

	public static function getChoices()
	{
		return array(
			self::MALE      => 'male',
			self::FEMALE    => 'female',
  		);

	}
}