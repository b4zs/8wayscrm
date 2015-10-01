<?php

namespace Application\CrmBundle\Enum;


class LeadStatus
{
	const PROSPECT = 'prospect';
	const WORKING = 'working';
	const HOT = 'hot';
	const SLEEPING = 'sleeping';
	const JUNK = 'junk';

	public static function getChoices()
	{
		$statuses = array_keys(self::getAllData());


		return array_combine($statuses, array_map('ucfirst', $statuses));
	}

	public static function getAllData()
	{
		return array(
			self::PROSPECT => array(
				'icon' => 'fa fa-user-plus',
				'title'=> 'Prospect',
			),
			self::WORKING => array(
				'icon'  => 'fa fa-steam',
				'title'=> 'Working',
			),
			self::HOT => array(
				'icon'  => 'fa fa-fire',
				'title'=> 'Hot',
			),
			self::SLEEPING => array(
				'icon'  => 'fa fa-bed',
				'title'=> 'Sleeping',
			),
			self::JUNK => array(
				'icon'  => 'fa fa-trash',
				'title'=> 'Junk',
			),
		);
	}

}