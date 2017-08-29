<?php


namespace Application\QuotationGeneratorBundle\ActionType;


use Application\ProjectAccountingBundle\Entity\Price;
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

        $state['quotation']['items'][] = $quotationItem;
        $total = isset($state['quotation']['total']) ? Price::fromArray($state['quotation']['total']) : new Price(0.0, 'EUR');

        $total->add($action->getQuotationItemPrice());

        $state['quotation']['total'] = $total->toArray();


        $answer->getFillOut()->setState($state);
    }
}