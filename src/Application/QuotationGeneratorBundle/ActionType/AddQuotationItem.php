<?php


namespace Application\QuotationGeneratorBundle\ActionType;


use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\QuestionAction;

class AddQuotationItem extends AbstractActionType
{


    public function execute(QuestionAction $action, FillOutAnswer $answer)
    {
        $state = $answer->getFillOut()->getState();
        if (!is_array($state['quotation'])) {
            throw new \RuntimeException('FillOut.state.quotation should be an array. '.json_encode($state['quotation']).' given.');
        }


        $quotationItem = [
            'name' => $action->getQuotationItemName(),
            'price' => $action->getQuotationItemPrice()->toArray(),
        ];

        $state['quotation'][] = $quotationItem;

//        var_dump($state);die;

        $answer->getFillOut()->setState($state);
    }
}