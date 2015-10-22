<?php


namespace Application\CrmBundle\Admin\Extension;


use Application\CrmBundle\Model\OwnerGroupAware;
use Application\UserBundle\Entity\User;
use Doctrine\Common\Util\Debug;
use FOS\UserBundle\Model\GroupInterface;
use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OwnerGroupManagerExtension extends AdminExtension
{
	/** @var  TokenStorageInterface */
	private $tokenStorage;

	/** @var  AuthorizationCheckerInterface */
	private $authorizationChecker;

	public function alterNewInstance(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->fetchGroup()) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			$object->setOwnerGroup($group);
		}
	}

	public function preUpdate(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->fetchGroup()) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			$object->setOwnerGroup($group);
		}
	}

	public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $context = 'list')
	{
		if (in_array('Application\\CrmBundle\\Model\\OwnerGroupAware', class_implements($admin->getClass())) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			$rootAliases = $query->getRootAliases();

			if ($group = $this->getGroupsOfCurrentUser()) {
				$query
					->andWhere(current($rootAliases).'.ownerGroup IN (:owner_group_filter)')
					->setParameter('owner_group_filter', $group);
			} else {
				$query->andWhere('1=0');
			}
		}
	}

	public function configureFormFields(FormMapper $form)
	{
		if ($form->getAdmin()->getSubject() instanceof OwnerGroupAware && $this->isGranted('ROLE_SUPER_ADMIN')) {
			$form->end();
			$form->with('Administration', array('class' => 'col-md-12 box-danger'));
			$form->add('ownerGroup', null, array(
				'label' => 'Owner group',
			));
			$form->end();
		}
	}

	private function fetchGroup()
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
				$requestedGroupId = $object->getOwnerGroup() ? $object->getOwnerGroup()->getId() : null;
				$availableGroupIds= array_map(function($g) { return $g->getId(); }, $this->getGroupsOfCurrentUser());
//				echo '<pre>#'.$object->getId();Debug::dump($object, 1);Debug::dump($object->getOwnerGroup());var_dump($requestedGroupId, $availableGroupIds);die;
				return in_array($requestedGroupId, $availableGroupIds);
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

	private function getGroupsOfCurrentUser()
	{
		if ($this->tokenStorage->getToken() && $user = $this->tokenStorage->getToken()->getUser()) {
			if ($user instanceof User) {
				$groups = $user->getGroups()->toArray();
				$groups[] = $user->getPrimaryGroup();
				return array_unique($groups);
			}
		}

		return array();
	}
}