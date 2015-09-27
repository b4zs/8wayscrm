<?php


namespace Application\CrmBundle\Enum;


class ProjectStatus
{

	const PENDING = 'pending';

	const IN_PROGRESS = 'in progress';

	const CLOSED = 'closed';

	public static function getChoices()
	{
		$statuses = array(
			self::PENDING, self::IN_PROGRESS, self::CLOSED,
		);

		return array_combine($statuses, array_map('ucfirst', $statuses));
	}

}