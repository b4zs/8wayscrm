<?php

namespace Core\LoggableEntityBundle\Block;

use Core\LoggableEntityBundle\Admin\Extension\LoggableEntityExtension;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Util\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityLogBlockService extends BaseBlockService
{
    /** @var ContainerInterface */
    private $container;

    /** @var  LoggableEntityExtension */
    private $loggableAdminExtension;

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (null === $blockContext->getBlock()->getId()) {
            $resolver = new OptionsResolver();
            $this->setDefaultSettings($resolver);

            $blockContext->getBlock()->setSettings($resolver->resolve($blockContext->getBlock()->getSettings()));
        }
        $className = $blockContext->getBlock()->getSetting('subject_class');
        $id = $blockContext->getBlock()->getSetting('subject_id');

        $entries = $this
            ->loggableAdminExtension
            ->buildLogEntriesQueryForEntity($className, $id)
            ->getResult();

        return $this->renderResponse($blockContext->getBlock()->getSetting('template'), array(
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
            'entries' => $entries,
        ), $response);
    }


    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'CoreLoggableEntityBundle:Block:entityLogBlock.html.twig',
            'subject_class' => null,
            'subject_id' => null,
        ));
    }

    public function setLoggableAdminExtension($loggableAdminExtension)
    {
        $this->loggableAdminExtension = $loggableAdminExtension;
    }


}