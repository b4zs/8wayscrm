<?php


namespace Application\QuotationGeneratorBundle\ActionType;


use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\QuestionAction;

class ImplyQuestion extends AbstractActionType
{
	public function execute(QuestionAction $action, FillOutAnswer $answer)
	{
		if ($action->getImpliedQuestion()) {
			$state = $answer->getFillOut()->getState();
			if (!is_array($state['questionStack'])) {
				throw new \RuntimeException('FillOut.state.questionStack should be an array. '.json_encode($state['questionStack']).' given.');
			}

			$impliedQuestionId = $action->getImpliedQuestion()->getId();
			if (reset($state['questionStack']) !== $impliedQuestionId && !in_array($impliedQuestionId, $state['questionStack'])) {
 			    array_unshift($state['questionStack'], $impliedQuestionId);
			}


			$answer->getFillOut()->setState($state);
		}
	}

}