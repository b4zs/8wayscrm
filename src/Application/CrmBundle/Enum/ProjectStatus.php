<?php


namespace Application\CrmBundle\Enum;


class ProjectStatus
{
	public static function getChoices()
	{
		$statuses = array(
			'new', 'in progress', 'closed',
		);

		return array_combine($statuses, $statuses);
	}

}