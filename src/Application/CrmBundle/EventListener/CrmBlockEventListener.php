<?php


namespace Application\CrmBundle\EventListener;


use Sonata\BlockBundle\Event\BlockEvent;

class CrmBlockEventListener
{
    public function onRenderFileCategories(BlockEvent $blockEvent)
    {
        $context = $blockEvent->getSetting('context');

        foreach ($context as $contextObject) {
            if (method_exists($contextObject, 'getId') && !$contextObject->getId()) {
                return;
            }
        }

        $block = new \Sonata\BlockBundle\Model\Block();
        $block->setType('application_crm.block.file_categories');
        $block->setSettings(array(
            'context' => $context,
            'limit'   => $blockEvent->getSetting('limit', 10),
        ));

        $blockEvent->addBlock($block);
    }
}