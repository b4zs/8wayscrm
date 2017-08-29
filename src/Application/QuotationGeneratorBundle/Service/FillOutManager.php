<?php


namespace Application\QuotationGeneratorBundle\Service;


use Application\QuotationGeneratorBundle\ActionType\AbstractActionType;
use Application\QuotationGeneratorBundle\Entity\FillOut;
use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\Question;
use Application\QuotationGeneratorBundle\Entity\QuestionAction;
use Application\QuotationGeneratorBundle\Entity\QuestionAnswer;
use Application\QuotationGeneratorBundle\Enum\ActionType;
use Application\QuotationGeneratorBundle\Model\PropertyAccessorWrapper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\VarDumper\VarDumper;

class FillOutManager
{
	/** @var EntityManager */
	private $entityManager;

	/** @var ContainerInterface */
	private $container;

	public function __construct(EntityManager $entityManager, ContainerInterface $container)
	{
		$this->entityManager = $entityManager;
		$this->container = $container;
	}

	public function updateFillOutState(FillOut $fillOut)
	{
		$fillOut->resetState();

        $initialQuestion = $this->getInitialQuestion();
        $questionStack = $this->getQuestionStack($fillOut);
        $questionStack[] = $initialQuestion->getId();
        $this->setQuestionStack($fillOut, $questionStack);

        //TODO: might has to befactored to recalculate qStack and reiterate the newly implied answers after each answer
        $fillOutQuestionsAnswers = [];
        foreach ($fillOut->getAnswers() as $answer) {
            $fillOutQuestionsAnswers[$answer->getQuestion()->getId()] = $answer;
        }

        $alreadyProcessedQuestions = [];

        while (true) {
            $this->log('QuestionStack at while loop iteration start: '.json_encode($questionStack));

            $managedToProcessAnything = false;
            foreach ($questionStack as $questionId) {
                if (in_array($questionId, $alreadyProcessedQuestions)) {
                    continue;
                }

                if (!isset($fillOutQuestionsAnswers[$questionId])) {
                    continue;
                }
                
                $answer = $fillOutQuestionsAnswers[$questionId];

                $this->processAnswer($answer);
                $questionStack = $this->getQuestionStack($fillOut);
                $this->log('QuestionStack after processing answer: '.json_encode($questionStack));

                $alreadyProcessedQuestions[] = $questionId;
                $managedToProcessAnything = true;
            }

            if (!$managedToProcessAnything) {
                $this->log('nothing has been processed');
                break;
            } else {
                $this->log('some items has been processed, redoing while loop');
            }


//            if (!in_array($answer->getQuestion()->getId(), $questionStack)) {
//                $this->log('Skipping Q#'.$answer->getQuestion()->getId(). ' A#'.$answer->getId(). ' due its not in qstack');
//                continue;
//            }
        }

		return $fillOut->getState();
	}

	public function processAnswer(FillOutAnswer $answer)
	{
	    $this->log('Processing Answer #'.$answer->getId());
        $fillout = $answer->getFillOut();
        $questionStack = $this->getQuestionStack($fillout);

//		$ix = array_search($answer->getQuestion()->getId(), $questionStack);
//		if (false !== $ix) {
//			unset($questionStack[$ix]);
//			$questionStack = array_values($questionStack);
//		}

		$this->setQuestionStack($fillout, $questionStack);

		$this->processAnswerActions($answer);
	}

	private function processAnswerActions(FillOutAnswer $answer)
	{
		$expressionLanguage = new ExpressionLanguage();

		$question = $answer->getQuestion();
		$scope = array(
			'answer'    => new PropertyAccessorWrapper($answer),
			'question'  => new PropertyAccessorWrapper($question),
			'user'      => new PropertyAccessorWrapper($this->getCurrentUser()),
		);

		foreach ($question->getActions() as $action) {
			$scope['action'] = new PropertyAccessorWrapper($action);

			try {
				$result = $expressionLanguage->evaluate($action->getCriteria(),	$scope);
				$this->log($action->getCriteria().' = '.json_encode($result));
			} catch (\RuntimeException $e) {
				$result = null;
			}

			if ($result) {
				$this->getActionServiceByType($action->getActionType())->execute($action, $answer);
			}
		}
	}

	/**
	 * @return AbstractActionType
	 */
	private function getActionServiceByType($type)
	{
		$serviceName = ActionType::mapValueToService($type);
		$service = $this->container->get($serviceName);

		if (!$service instanceof AbstractActionType) {
			throw new \RuntimeException(sprintf('Service(%s) not registered for type(%s)', $serviceName, $type));
		}

		return $service;
	}

	private function getInitialQuestion()
	{
		$question = $this->getQuestionRepository()->findOneBy(array('alias' => 'START'));

		if (null === $question) {
			throw new EntityNotFoundException('No Question found with alias=START');
		}

		return $question;
	}

	private function getQuestionRepository()
	{
		return $this->entityManager->getRepository('ApplicationQuotationGeneratorBundle:Question');
	}

	private function getCurrentUser()
	{
		$context = $this->container->get('security.token_storage');
		if (null === $context->getToken()) {
			throw new UnauthorizedHttpException('Not authorized');
		} else {
			return $context->getToken()->getUser();
		}
	}

    public function getQuestionStack(FillOut $fillout)
    {
        $state = $fillout->getState();
        $questionStack = $state['questionStack'];
        if (!is_array($questionStack)) {
            throw new \RuntimeException('FillOut.state.questionStack should be an array. ' . json_encode($questionStack) . ' given.');
        }

        return $questionStack;
    }

    private function setQuestionStack(FillOut $fillout, array $questionStack)
    {
        $state = $fillout->getState();
        $state['questionStack'] = $questionStack;
        $fillout->setState($state);
    }

    private function log($string)
    {
//        echo $string.PHP_EOL;
    }

}