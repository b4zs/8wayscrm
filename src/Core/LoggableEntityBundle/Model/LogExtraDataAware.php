<?php


namespace Core\LoggableEntityBundle\Model;


interface LogExtraDataAware
{
	/**
	 * @return LogExtraData|null
	 */
	public function getLogExtraData();

	public function setLogExtraData(LogExtraData $logExtraData);

	public function setUpdatedAt(\DateTime $datTime);
}