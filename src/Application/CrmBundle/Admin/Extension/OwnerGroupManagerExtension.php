<?php


namespace Application\CrmBundle\Admin\Extension;


use Application\CrmBundle\Model\OwnerGroupAware;
use Application\CrmBundle\Security\CrmSecurityHelper;
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

class OwnerGroupManagerExtension extends AdminExtension
{

	/** @var CrmSecurityHelper */
	private $securityHelper;

	public function __construct(CrmSecurityHelper $securityHelper)
	{
		$this->securityHelper = $securityHelper;
	}


	public function alterNewInstance(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->securityHelper->fetchGroupOfCurrentUser()) && !$this->securityHelper->isGranted('ROLE_SUPER_ADMIN')) {
			$object->addGroup($group);

			if ($object instanceof User) {
				$object->setPrimaryGroup($group);
			}
		}
	}

	public function prePersist(AdminInterface $admin, $object)
	{
		if ($object instanceof OwnerGroupAware && null !== ($group = $this->securityHelper->fetchGroupOfCurrentUser()) && !$this->securityHelper->isGranted('ROLE_SUPER_ADMIN')) {
			if ($object->getGroups()->count() < 1) {
				$object->addGroup($group);
			}
		}
	}

	public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $context = 'list')
	{
		if ($this->isActiveOnAdmin($admin) && !$this->securityHelper->isGranted('ROLE_SUPER_ADMIN')) {
			$rootAliases = $query->getRootAliases();

			if ($group = $this->securityHelper->getGroupsOfCurrentUser()) {
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
		if ($form->getAdmin()->getSubject() instanceof OwnerGroupAware && $this->securityHelper->isGranted('ROLE_SUPER_ADMIN') && !$form->getAdmin()->getSubject() instanceof UserInterface) {
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
		if ($this->isActiveOnAdmin($list->getAdmin()) && $this->securityHelper->isGranted('ROLE_SUPER_ADMIN')){
			$list->add('groups', null, array(
				'label' => 'Groups (SA)'
			));
		};
	}

	private function isActiveOnAdmin(AdminInterface $admin)
	{
		return in_array('Application\\CrmBundle\\Model\\OwnerGroupAware', class_implements($admin->getClass()));
	}

	public function isGranted($role, $object = null)
	{
		return $this->securityHelper->isGranted($role, $object);
	}
}