<?php

namespace Application\CrmBundle\Security;


use Application\CrmBundle\Model\OwnerGroupAware;
use Application\UserBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\UserInterface;
use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class CrmSecurityHelper
{
	/** @var  TokenStorageInterface */
	private $tokenStorage;

	/** @var  AuthorizationCheckerInterface */
	private $authorizationChecker;

	/**
	 * @param TokenStorageInterface $tokenStorage
	 * @param AuthorizationCheckerInterface $authorizationChecker
	 */
	public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->tokenStorage = $tokenStorage;
		$this->authorizationChecker = $authorizationChecker;
	}


	public function fetchGroupOfCurrentUser()
	{
		if ($this->tokenStorage->getToken() && $user = $this->tokenStorage->getToken()->getUser()) {
			if ($user instanceof User) {
				return $user->getPrimaryGroup();
			}
		}

		return null;
	}

	public function isGranted($role, $object = null)
	{
		if ($object instanceof OwnerGroupAware && in_array($role, array('EDIT', 'SHOW', 'DELETE'))) {
			if ($this->isGranted('ROLE_SUPER_ADMIN')) {
				return true;
			} else {
				$extractId = function ($g) { return $g->getId(); };
				$requestedGroupIds = array_map($extractId, $object->getGroups()->toArray());
				$availableGroupIds= array_map($extractId, $this->getGroupsOfCurrentUser());
				return count(array_intersect($requestedGroupIds, $availableGroupIds)) > 0;
			}
		}
		return $this->authorizationChecker->isGranted($role, $object);
	}

	public function setTokenStorage(TokenStorageInterface $tokenStorage)
	{
		$this->tokenStorage = $tokenStorage;
	}

	public function setAuthorizationChecker($authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}

	public function getGroupsOfCurrentUser()
	{
		if ($this->tokenStorage->getToken() && $user = $this->tokenStorage->getToken()->getUser()) {
			if ($user instanceof User) {
				$groups = $user->getGroups()->toArray();
                if(null !== $user->getPrimaryGroup()) {
                    $groups[] = $user->getPrimaryGroup();
                }
				return array_unique($groups);
			}
		}

		return array();
	}


}