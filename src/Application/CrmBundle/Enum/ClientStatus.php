<?php

namespace Application\CrmBundle\Enum;


class ClientStatus
{
	public static function getChoices()
	{
		$statuses = array(
			'new', 'working', 'HOT', 'sleeping', 'junk', 'blacklisted',
		);

		return array_combine($statuses, $statuses);

	}

}