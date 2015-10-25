<?php


namespace Application\CrmBundle\Model;


use FOS\UserBundle\Model\GroupInterface;

interface OwnerGroupAware
{
	public function getGroups();
	public function addGroup(GroupInterface $group);
	public function removeGroup(GroupInterface $group);

}