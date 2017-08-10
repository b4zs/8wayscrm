<?php

namespace Application\QuotationGeneratorBundle\Admin;

use Application\QuotationGeneratorBundle\Entity\FillOut;
use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\Question;
use Application\QuotationGeneratorBundle\Enum\FormType;
use Application\QuotationGeneratorBundle\Form\YamlArrayType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\VarDumper\VarDumper;

class FillOutAnswerAdmin extends Admin
{
    public function getFillOutManager()
    {
        return $this->configurationPool->getContainer()->get('application_quotation_generator.fillout_manager');
    }

    /**
     * @return object
     */
    public function loadQuestion($questionId)
    {
        $questionClass = get_class(new Question());
        $question = $this->modelManager->find($questionClass, $questionId);
        return $question;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('value')
            ->add('createdAt')
//            ->add('step')
            ->add('data')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('question')
            ->add('value')
//            ->add('data', null, array(
//                'template' => 'ApplicationQuotationGeneratorBundle:Admin:object_field.html.twig'
//            ))
            ->add('createdAt')
//            ->add('step')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->hasSubject()) {
            if (null === $this->getSubject()->getQuestion()) {
                $formMapper->add('question', null);
            } else {
                $question = $this->getSubject()->getQuestion();

                $formMapper->with($question->getText());
//                $formMapper->add('question', null, array('read_only' =>  true, 'attr' => array('readonly' => 'readonly')));

                if ($question->getOptions()->count() > 0 && in_array($question->getFormType(), FormType::getOptionBasedTypes())) {
                    $formMapper->add('option', 'entity', array(
                        'label'     => 'Option',
                        'class'     => 'Application\\QuotationGeneratorBundle\\Entity\\QuestionOption',
                        'required'  => false,
                        'query_builder' =>  function (EntityRepository $er) use ($question) {
                            return $er->createQueryBuilder('qo')
                                ->andWhere('qo.question = :question')
                                ->setParameter('question', $question);
                        },
                    ));
                } else {
                    $formMapper->add('value', null, array('required' => false,));
                }
                $formMapper->add('data', new YamlArrayType());

            }

        }
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('value')
            ->add('createdAt')
//            ->add('step')
            ->add('data')
        ;
    }

    public function getClassnameLabel()
    {
        return 'Answers';
    }

    /**
     * @return FillOutAnswer
     */
    public function getNewInstance()
    {
        /** @var FillOutAnswer $object */
        $object = parent::getNewInstance();

        if ($this->isChild()) {
            $object->setFillOut($this->getParent()->getSubject());
        }

        if ($this->hasRequest() && $questionId = $this->getRequest()->get('questionId')) {
            $object->setQuestion($this->loadQuestion($questionId));
        }

        return $object;
    }

    /**
     * @return FillOutAnswer
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    public function toString($object)
    {
        /** @var FillOutAnswer $object */
        return sprintf(
            'F#%s > Q#%s > V#%s',
            $object->getFillOut() ? $object->getFillOut()->getId() : null,
            $object->getQuestion() ? $object->getQuestion()->getId() : null,
            $object->getValue()
        );
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'ApplicationQuotationGeneratorBundle:FillOutAdmin:list.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function buildQuestionStackList(FillOut $fillOut)
    {
        $state = $this->getFillOutManager()->updateFillOutState($fillOut);
        $fillOutAnswerAdmin = $this;

        $result = array();
        foreach ($state['questionStack'] as $questionId) {
            /** @var Question $question */
            $question = $this->loadQuestion($questionId);
            if (null === $question) {
                throw new \RuntimeException(sprintf('Question#%d does not exists', $questionId));
            }
            $result[$fillOutAnswerAdmin->generateUrl('create', array('questionId' => $questionId))] = $question->getText();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        if ($this->isChild() && $this->getParent()->getSubject() instanceof FillOut) {
            $query
                ->andWhere('o.fillOut = :fillout')
                ->setParameter('fillout', $this->getParent()->getSubject());
        }

        return $query;
    }

    public function getPersistentParameters()
    {
        $parameters = parent::getPersistentParameters();

        if ($this->hasRequest() && $questionId = $this->getRequest()->get('questionId')) {
            $controller = explode('::', $this->getRequest()->attributes->get('_controller'));
            if ('createAction' === $controller[1]) {
                $parameters['questionId'] = $questionId;
            }
        }

        return $parameters;
    }


}
