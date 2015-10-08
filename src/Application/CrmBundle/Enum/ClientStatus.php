<?php

namespace Application\CrmBundle\Enum;


class ClientStatus
{
	const COLD = 'cold';
	const HOT = 'hot';
	const SLEEPING = 'sleeping';
	const ACTIVE = 'active';
	const ARCHIVED = 'archived';
	const JUNK = 'junk';

	public static function getChoices()
	{
		$statuses = array_keys(self::getAllData());


		return array_combine($statuses, array_map('ucfirst', $statuses));
	}

	public static function getAllData()
	{
		return array(
			self::COLD => array(
				'icon' => 'fa fa-user-plus',
				'title'=> 'Cold',
			),
			self::HOT => array(
				'icon'  => 'fa fa-fire',
				'title'=> 'Hot',
			),
			self::SLEEPING => array(
				'icon'  => 'fa fa-bed',
				'title'=> 'Sleeping',
			),
			self::ACTIVE => array(
				'icon'  => 'fa fa-bed',
				'title'=> 'Active',
			),
			self::ARCHIVED => array(
				'icon'  => 'fa fa-bed',
				'title'=> 'Archived',
			),
			self::JUNK => array(
				'icon'  => 'fa fa-trash',
				'title'=> 'Junk',
			),
		);
	}

}