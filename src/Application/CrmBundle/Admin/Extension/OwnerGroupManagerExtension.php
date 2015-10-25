<?php


namespace Application\CrmBundle\Admin\Extension;


use Application\CrmBundle\Model\OwnerGroupAware;
use Application\UserBundle\Entity\User;
use Doctrine\Common\Util\Debug;
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

class OwnerGroupManagerExtension extends AdminExtension
{
	/** @var  TokenStorageInterface */
	private $tokenStorage;

	/** @var  AuthorizationCheckerInterface */
	private $authorizationChecker;

	public function alterNewInstance(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->fetchGroupOfCurrentUser()) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			$object->addGroup($group);

			if ($object instanceof User) {
				$object->setPrimaryGroup($group);
			}
		}
	}

	public function preUpdate(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->fetchGroupOfCurrentUser()) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
//			$object->addGroup($group);
		}
	}

	public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $context = 'list')
	{
		if ($this->isActiveOnAdmin($admin) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			$rootAliases = $query->getRootAliases();

			if ($group = $this->getGroupsOfCurrentUser()) {
				$query
					->innerJoin(current($rootAliases).'.groups', 'groups')
					->andWhere('groups.id IN (:owner_group_filter)')
					->setParameter('owner_group_filter', $group);
			} else {
				$query->andWhere('1=0');
			}
		}
	}

	public function configureFormFields(FormMapper $form)
	{
		if ($form->getAdmin()->getSubject() instanceof OwnerGroupAware && $this->isGranted('ROLE_SUPER_ADMIN') && !$form->getAdmin()->getSubject()instanceof UserInterface) {
			$form->end();
			$form->with('Administration', array('class' => 'col-md-12 box-danger'));
			$form->add('groups', null, array(
				'label' => 'Group',
			));
			$form->end();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureListFields(ListMapper $list)
	{
		if ($this->isActiveOnAdmin($list->getAdmin()) && $this->isGranted('ROLE_SUPER_ADMIN')){
			$list->add('groups', null, array(
				'label' => 'Groups (SA)'
			));
		};
	}


	private function fetchGroupOfCurrentUser()
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

	private function isActiveOnAdmin(AdminInterface $admin)
	{
		return in_array('Application\\CrmBundle\\Model\\OwnerGroupAware', class_implements($admin->getClass()));
	}
}