<?php

namespace Application\QuotationGeneratorBundle\Controller;


use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("question")
 */
class QuestionApiController extends FOSRestController
{
	public function cgetAction()
	{
		$repository = $this->getQuestionRepository();

		$queryBuilder = $repository->createQueryBuilder('question');
		$searchTerm = $this->container->get('request')->query->get('q');
		if ($searchTerm) {
			$queryBuilder->andWhere('question.text LIKE :term');
			$queryBuilder->setParameter('term', '%'.$searchTerm.'%');
		}

		$data = $queryBuilder->getQuery()->getResult();

		return $this->handleView($this->view($data));
	}

	public function getAction($id)
	{
		return $this->handleView($this->view($this->getQuestionRepository()->find($id)));
	}

	public function getEvalAction($questionId)
	{
		$answer = $this->container->get('request')->get('answer');
		$repository = $this
			->container
			->get('doctrine')
			->getRepository('ApplicationQuotationGeneratorBundle:Question');

		$question = $repository->find($questionId);
		$expressionLanguage = new ExpressionLanguage();
		$suitableActions = array();

		foreach ($question->getActions() as $action) {
			$result = $expressionLanguage->evaluate(
				$action->getCriteria(),
				array(
					'answer' => $answer,
					'params' => (object)$action->getActionParams(),
					'user'   => array(), //TODO
				)
			);
			if ($result) {
				$suitableActions[] = $action;
			}
		}

		return $this->handleView($this->view($suitableActions));
	}


	/**
	 * @return EntityRepository
	 */
	protected function getQuestionRepository()
	{
		$repository = $this
			->container
			->get('doctrine')
			->getRepository('ApplicationQuotationGeneratorBundle:Question');
		return $repository;
	}

}