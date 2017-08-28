<?php


namespace Application\QuotationGeneratorBundle\ActionType;


use Application\QuotationGeneratorBundle\Entity\FillOutAnswer;
use Application\QuotationGeneratorBundle\Entity\QuestionAction;

class ImplyQuestion extends AbstractActionType
{
    public function execute(QuestionAction $action, FillOutAnswer $answer)
    {
        $state = $answer->getFillOut()->getState();
        if (!is_array($state['questionStack'])) {
            throw new \RuntimeException('FillOut.state.questionStack should be an array. '.json_encode($state['questionStack']).' given.');
        }

        $questionsToImply = [];

        $questionsRepository = $this->container->get('doctrine.orm.entity_manager')->getRepository('ApplicationQuotationGeneratorBundle:Question');
        $extractId = function($record) { return $record->getId(); };


        foreach ($action->getImplyQuestionsBySelection() as $question) {
            $questionsToImply[] = $question->getId();
        }

        if ($action->getImplyQuestionsByTags()->count() > 0) {
            $questionsIdsByTag = $questionsRepository
                ->createQueryBuilder('q')
                ->select('q.id as q_id')
                ->innerJoin('q.tags', 'tag')
                ->andWhere('tag.id in (:tags)')
                ->setParameter('tags', array_map($extractId, $action->getImplyQuestionsByTags()->toArray()))
                ->getQuery()
                ->getResult();

            foreach ($questionsIdsByTag as $questionIdByTag) {
                $questionsToImply[] = $questionIdByTag['q_id'];
            }
        }

        if ($action->getImplyQuestionsByGroups()->count() > 0) {
            $questionsIdsByGroups = $questionsRepository
                ->createQueryBuilder('q')
                ->select('q.id as q_id')
                ->innerJoin('q.group', 'group')
                ->andWhere('group.id in (:groups)')
                ->setParameter('groups', array_map($extractId, $action->getImplyQuestionsByGroups()->toArray()))
                ->getQuery()
                ->getResult();

            foreach ($questionsIdsByGroups as $questionIdsByGroups) {
                $questionsToImply[] = $questionIdsByGroups['q_id'];
            }
        }

        foreach ($questionsToImply as $impliedQuestionId) {
            if (reset($state['questionStack']) !== $impliedQuestionId && !in_array($impliedQuestionId, $state['questionStack'])) {
                array_unshift($state['questionStack'], $impliedQuestionId);
            }
        }


        $answer->getFillOut()->setState($state);
    }

}