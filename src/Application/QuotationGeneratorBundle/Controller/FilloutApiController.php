<?php

namespace Application\QuotationGeneratorBundle\Controller;

use Application\QuotationGeneratorBundle\Entity\FillOut;
use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Form\FillOutAnswerType;
use Application\QuotationGeneratorBundle\Form\FilloutType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @RouteResource("fillout")
 */
class FilloutApiController extends FOSRestController
{
	public function cgetAction()
	{
		$repository = $this->getFilloutRepository();

		$data = $repository->findAll();

		return $this->handleView($this->view($data));
	}
	public function getAction($id)
	{
		return $this->handleView($this->view($this->getFilloutRepository()->find($id)));
	}

	public function postAction()
	{
		$request = $this->container->get('request');
		$form = $this->createForm(new FilloutType());

		$form->submit($request->request->all());
		if ($form->isValid()) {
			/** @var FillOut $fillout */
			$fillout = $form->getData();
			$entityManager = $this->container->get('doctrine.orm.entity_manager');
			$entityManager->persist($fillout);
			$this->initializeFilloutState($entityManager, $fillout);
			$entityManager->flush();

			return $this->handleView($this->view($fillout));
		} else {
			throw new BadRequestHttpException($form->getErrorsAsString());
		}
	}

	public function putAction($id)
	{
		$fillout = $this->getFilloutRepository()->find($id);

		//TODO: save!

		return $this->handleView($this->view($fillout));
	}

	public function getAnswersAction($id)
	{
		/** @var FillOut $fillout */
		$fillout = $this->getFilloutRepository()->find($id);

		$answers = $this->buildFilloutAnswersArray($fillout);

		return $this->handleView($this->view($answers));
	}

	public function postAnswersAction($id)
	{
		$request = $this->get('request');
		$fillout = $this->loadFillout($id);
		$submittedData = $request->request->all();

		foreach ($submittedData as $submittedAnswerData) {
			$answer = new FillOutAnswer();
			$answer->setFillOut($fillout);

			$form = $this->createForm(new FillOutAnswerType(), $answer, array());
			$form->submit($submittedAnswerData);
			if ($form->isValid()) {
				$fillout->addAnswer($answer);
				$this->getFilloutManager()->processAnswer($answer);
			} else {
				throw new BadRequestHttpException($form->getErrorsAsString());
			}
		}

		$this->getEntityManager()->flush();

		$answers = $this->buildFilloutAnswersArray($fillout);

		return $this->handleView($this->view($answers));
	}

	public function putAnswersAction($id)
	{
		return $this->postAnswersAction($id);
	}

	public function deleteAnswerAction($id, $questionId)
	{
		$fillOut = $this->loadFillout($id);
		$toDelete = $fillOut->getAnswers()->filter(function(FillOutAnswer $answer) use($questionId){
			return $answer->getQuestionId() == $questionId;
		});

		foreach ($toDelete as $answerToDelete) {
			$fillOut->removeAnswer($answerToDelete);
		};

		$this->getEntityManager()->flush();

		$answers = $this->buildFilloutAnswersArray($fillOut);

		return $this->handleView($this->view($answers));
	}




	protected function getFilloutRepository()
	{
		$repository = $this
			->container
			->get('doctrine')
			->getRepository('ApplicationQuotationGeneratorBundle:FillOut');
		return $repository;
	}

	/**
	 * @return FillOut
	 */
	protected function loadFillout($id)
	{
		$fillout = $this->getFilloutRepository()->find($id);
		return $fillout;
	}

	private function getEntityManager()
	{
		return $this->get('doctrine.orm.default_entity_manager');
	}

	private function getFilloutManager()
	{
		return $this->get('application_quotation_generator.fillout_manager');
	}

	/**
	 * @return mixed
	 */
	protected function loadQuestion($questionId)
	{
		$questionRepository = $this->getDoctrine()->getRepository('ApplicationQuotationGeneratorBundle:Question');
		$question = $questionRepository->find($questionId);
		if (null === $question) {
			throw new \RuntimeException('Question with id ' . $questionId . ' was not found');
		}

		return $question;
	}

	/**
	 * @return array
	 */
	protected function buildFilloutAnswersArray(FillOut $fillout)
	{
		$answers = $fillout->buildAnswersForApi();

		$state = $fillout->getState();
		$questionStack = $state['questionStack'];


		foreach ($questionStack as $questionId) {
			$answers[] = $answer = new FillOutAnswer();
			$answer->setQuestion($this->loadQuestion($questionId));
		}
		return $answers;
	}

	protected function initializeFilloutState($entityManager, $fillout)
	{
		$initialQuestion = $entityManager->getRepository('ApplicationQuotationGeneratorBundle:Question')->findOneBy(array(
			'text' => '__INIT__',
		));
		$initialAnswer = new FillOutAnswer();
		$initialAnswer->setQuestion($initialQuestion);
		$initialAnswer->setValue('__INIT__');
		$fillout->addAnswer($initialAnswer);
		$this->getFilloutManager()->processAnswer($initialAnswer);
	}
}