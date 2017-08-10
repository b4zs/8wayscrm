<?php

namespace Application\QuotationGeneratorBundle\Admin;

use Application\QuotationGeneratorBundle\Entity\QuestionAction;
use Application\QuotationGeneratorBundle\Enum\ActionType;
use Application\QuotationGeneratorBundle\Form\YamlArrayType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class QuestionActionAdmin extends Admin
{
	/**
	 * {@inheritdoc}
	 */
	public function getNewInstance()
	{
		if ($this->hasRequest() && $copyFromId = $this->getRequest()->get('copy_from')) {
			$original = $this->getModelManager()->find($this->getClass(), $copyFromId);
			$i = clone $original;
		} else {
			$i = parent::getNewInstance();
		}

		/** @var QuestionAction $i */

		if ($this->isChild()) {
			$i->setQuestion($this->getParent()->getSubject());
		}

		$i->setCriteria('answer.value == action.option.value');
		$i->setActionType(ActionType::IMPLY_QUESTION);

		return $i;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		if ($this->isChild()) {
			$query
				->andWhere('o.question = :p')
				->setParameter('p', $this->getParent()->getSubject());
		}


		return $query;
	}

	/**
	 * @param DatagridMapper $datagridMapper
	 */
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('criteria')
			->add('actionType')
			->add('actionParams');
	}

	/**
	 * @param ListMapper $listMapper
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		if (!$this->isChild()) {
			$listMapper->addIdentifier('question');
		}

		$self = $this;
		$listMapper
			->addIdentifier('criteria', null, array(
				'template'  => 'ApplicationQuotationGeneratorBundle:Admin:criteria_field.html.twig',
				'compiler'  => function($value, $object) use ($self){
					return $self->compile($value, $object);
				}
			))
			->add('actionType', null, array(
				'template'  => 'ApplicationQuotationGeneratorBundle:Admin:enum_field.html.twig',
				'choices'   => ActionType::getChoices(),
			))
			->addIdentifier('questionOption')
			->add('impliedQuestion')
//			->add('actionParams', null, array(
//				'template' => 'ApplicationQuotationGeneratorBundle:Admin:object_field.html.twig'
//			))
			->add('_action', 'actions', array(
				'actions'   => array(
					'edit'      => array(),
					'delete'    => array(),
					'copy'    => array(
						'template' => 'ApplicationQuotationGeneratorBundle:Admin:list_action_copy.html.twig'
					),
				)
			));
	}

	/**
	 * @param FormMapper $formMapper
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$action = $this->hasSubject() ? $this->getSubject() : null;
		$question = $action ? $action->getQuestion() : null;

		$formMapper->with('Condition', array('class' => 'col-md-6', ));

		$formMapper->add('criteria', null, array(
			'label' => 'Criteria (twig code, the "answer" and "action" object is accessible)'
		));

		if (null !== $question && $question->getOptions()->count() > 0) {
			$formMapper->add('questionOption', 'entity', array(
				'label'     => 'Option',
				'class'     => 'Application\\QuotationGeneratorBundle\\Entity\\QuestionOption',
				'required'  => false,
				'query_builder' =>  function (EntityRepository $er) use ($question) {
					return $er->createQueryBuilder('qo')
						->andWhere('qo.question = :question')
						->setParameter('question', $question);
				},
			));
		}

		$formMapper->end()->with('Action', array('class' => 'col-md-6'));
		$formMapper->add('actionType', 'choice', array(
			'label' => 'Type',
			'choices' => ActionType::getChoices(),
		));


//		var_dump($action);die;

		if ($action && $action->getActionType() === ActionType::IMPLY_QUESTION) {
			$formMapper->add('impliedQuestion', 'sonata_type_model_list', array(
				'btn_delete'        => false,
			), array());
		}

		$formMapper->add('actionParams', new YamlArrayType(), array(
			'label' => 'Params (yaml)',
			'required' => false,
			'attr' => array(
				'rows' => 10,
			),
		));
	}

	/**
	 * @param ShowMapper $showMapper
	 */
	protected function configureShowFields(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('criteria')
			->add('actionType')
			->add('actionParams')
			->add('createdAt')
			->add('deletedAt');
	}

	/**
	 * @return QuestionAction
	 */
	public function getSubject()
	{
		return parent::getSubject();
	}

	public function compile($code, QuestionAction $action)
	{
		$scope = new \stdClass();
		$scope->action = $action;
		$propertyAccessor = new PropertyAccessor(true, false);
		try {
			$value = $propertyAccessor->getValue($scope, 'action.option.value');
		} catch (UnexpectedTypeException $e) {
			$value = null;
		}

		return strtr($code, array(
			'action.option.value' => json_encode($value),
		));
	}


}
