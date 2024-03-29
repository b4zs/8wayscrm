<?php

namespace Application\QuotationGeneratorBundle\Controller;

use Application\QuotationGeneratorBundle\Entity\FillOut;
use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\Question;
use Application\QuotationGeneratorBundle\Entity\QuestionGroup;
use Application\QuotationGeneratorBundle\Entity\QuestionOption;
use Application\QuotationGeneratorBundle\Enum\ActionType;
use Application\QuotationGeneratorBundle\Form\FillOutAnswerType;
use Application\QuotationGeneratorBundle\Form\FilloutType;
use Application\QuotationGeneratorBundle\Service\FillOutManager;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
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


    private function buildQuestion(Question $question)
    {
        $request = $this->container->get('request_stack')->getMasterRequest();
        $basePath = $request->getSchemeAndHttpHost().$request->getBasePath();


        $result = array();
        $result['questionId'] = $question->getId();
        $result['title'] = $question->getText();
        $result['hint'] = nl2br($question->getHint());
        $result['type'] = $question->getFormType();
        $result['group'] = $question->getGroup() ? $question->getGroup()->getName() : null;
        $result['stage'] = $question->getStage();
        $result['alias'] = $question->getAlias();
        $result['category'] = $question->getCategory() ? $question->getCategory()->getName() : null;
        $result['requiredUserRole'] = $question->getRequiredUserRole();

        if ($question->getOptions()->count()) {
            $options = array();
            /** @var QuestionOption $option */
            foreach ($question->getOptions() as $questionOption) {
                $option = array(
                    'label' => $questionOption->getText(),
                    'value' => $questionOption->getValue(),
                    'hint'  => nl2br($questionOption->getHint()),
                    'quotation' => $this->calculateQuestionOptionPrice($questionOption),
                );

                if ($questionOption->getMedia()) {
                    $mediaUrl = $this->container->get('sonata.media.pool')
                        ->getProvider($questionOption->getMedia()->getProviderName())
                        ->generatePublicUrl($questionOption->getMedia(), 'reference');
                    $mediaUrl = $basePath . $mediaUrl;

                    $option['image'] = $mediaUrl;
                }

                $options[] = $option;
            }
            $result['choices'] = $options;
        }
        
        return $result;
    }

    private function buildAnswer(FillOutAnswer $answer)
    {
        $question = $this->buildQuestion($answer->getQuestion());
        $question['answerId'] = $answer->getId();
        switch ($question['type']) {
            case 'number':
                $question['value'] = floatval($answer->getValue());
                break;
            default:
                $question['value'] = $answer->getValue();
        }

        return $question;
    }

    public function getAction($id)
    {
        /** @var FillOut $fillout */
        $fillout = $this->loadFillout($id);
        $this->getFilloutManager()->updateFillOutState($fillout);
        $responseData = $this->buildResponse($fillout);
        return $this->handleView($this->view($responseData));
    }

    public function postAction($id)
    {
        /** @var Request $request */
        $request = $this->container->get('request');

        /** @var FillOut $fillout */
        $fillout = $this->loadFillout($id);

        $questionsData = $request->request->get('questions');
//        var_dump($questionsData);die;

        foreach ($questionsData as $questionData) {
            if (!empty($questionData['answerId'])) {
                $answer = $fillout->getAnswers()->filter(function(FillOutAnswer $answer) use ($questionData) {
                    return $answer->getId() ===  $questionData['answerId'];
                })->first();
            } else {
                $answer = new FillOutAnswer();
                $answer->setQuestion($this->loadQuestion($questionData['questionId']));
            }

            if (!empty($questionData['dirty'])) {
                $answer->setValue($questionData['value']);

                if (!$answer->getId()) {
                    $fillout->addAnswer($answer);
                    $this->getEntityManager()->persist($answer);
                }
            }
        }

        $this->getFilloutManager()->updateFillOutState($fillout);
        $this->getEntityManager()->flush();

        $responseData = $this->buildResponse($fillout);
        return $this->handleView($this->view($responseData));
//        $form = $this->createForm(new FilloutType());
//
//        $form->submit($request->request->all());
//
//        if ($form->isValid()) {
//            /** @var FillOut $fillout */
//            $fillout = $form->getData();
//            $entityManager = $this->container->get('doctrine.orm.entity_manager');
//            $entityManager->persist($fillout);
//            $this->initializeFilloutState($entityManager, $fillout);
//            $entityManager->flush();
//
//            return $this->handleView($this->view($fillout));
//        } else {
//            throw new BadRequestHttpException($form->getErrorsAsString());
//        }
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
        $toDelete = $fillOut->getAnswers()->filter(function (FillOutAnswer $answer) use ($questionId) {
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
     * @return Question
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

    private function buildFilloutQuestions(FillOut $fillout)
    {
        $questionsResult = array();
        $questionStack = $this->getFilloutManager()->getQuestionStack($fillout);

        foreach ($fillout->getAnswers() as $answer) {
            if (!in_array($answer->getQuestion()->getId(), $questionStack)) continue;

            $question = $this->buildAnswer($answer);
            $questionsResult[] = $question;
        }

        /** @var FillOutManager $filloutManager */
        $filloutManager = $this->container->get('application_quotation_generator.fillout_manager');

        $questionIds = array();
        foreach ($questionsResult as $questionResult) {
            $questionIds[] = $questionResult['questionId'];
        }

        foreach ($questionStack as $questionId) {
            if (in_array($questionId, $questionIds)) continue;

            $questionFromStack = $this->buildQuestion($question = $this->loadQuestion($questionId));
            $questionIds[] = $questionFromStack['questionId'];
            $questionsResult[] = $questionFromStack;
        }

        usort($questionsResult, function($a, $b) {
            $av = $a['questionId'];
            $bv = $b['questionId'];
//            $av = isset($a['answerId']) ? $a['answerId'] : $a['questionId'];
//            $bv = isset($b['answerId']) ? $b['answerId'] : $b['questionId'];

            return $av < $bv
               ? -1
               : 1;
        });

        return $questionsResult;
    }

    private function calculateQuestionOptionPrice(QuestionOption $questionOption)
    {
        foreach ($questionOption->getActions() as $questionAction) {
            if (ActionType::ADD_QUOTATION_ITEM === $questionAction->getActionType()) {
                $quotation = array(
                    'price' => $questionAction->getQuotationItemPrice()->toArray(),
                    'name' => $questionAction->getQuotationItemName(),
                );

                return $quotation;
            }
        }

        return null;
    }

    protected function buildResponse(FillOut $fillout)
    {
        $questionsResult = $this->buildFilloutQuestions($fillout);
        $state = $fillout->getState();
        $quotation = $state['quotation'];

        $groups = array();
        foreach ($questionsResult as $questionResult) {
            $questionId = $questionResult['questionId'];
            $question = $this->loadQuestion($questionId);

            if (!$question->getGroup()) {
                $groups[0] = array(
                    'name' => 'default',
                    'label' => 'default',
                    'class' => '',
                    'position' => 0,
                    'id' => null,
                );
            } else {
                $groups[$question->getGroup()->getId()] = array(
                    'id' => $question->getGroup()->getId(),
                    'name' => $question->getGroup()->getName(),
                    'label' => $question->getGroup()->getLabel(),
                    'class' => $question->getGroup()->getClass(),
                    'position' => $question->getGroup()->getPosition(),
                );
            }

        }

        $responseData = array(
            'questions'     => $questionsResult,
            'questionStack' => $fillout->getState()['questionStack'],
            'quotation'     => $quotation,
            'groups'        => array_values($groups),
        );
        return $responseData;
    }
}