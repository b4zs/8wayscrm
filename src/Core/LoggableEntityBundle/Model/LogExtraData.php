<?php


namespace Core\LoggableEntityBundle\Model;


class LogExtraData
{
	public $comment;

	public $customAction;

	public $extraData = array();

	public function hasData()
	{
		foreach ($this as $v) {
			if (!empty($v)) {
				return true;
			}
		}

		return false;
	}

}