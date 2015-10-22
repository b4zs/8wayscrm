<?php


namespace Application\CrmBundle\Model;


use FOS\UserBundle\Model\GroupInterface;

interface OwnerGroupAware
{
	public function getOwnerGroup();
	public function setOwnerGroup(GroupInterface $group);

}