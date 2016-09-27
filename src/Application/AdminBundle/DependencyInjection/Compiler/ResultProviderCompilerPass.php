<?php

namespace Application\AdminBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResultProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->has('application_admin.util.search.result_provider_pool')) {
            return;
        }

        $definition = $container->findDefinition(
            'application_admin.util.search.result_provider_pool'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'admin.result_provider'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addProvider',
                    array(new Reference($id), $attributes["class"])
                );
            }
        }
    }

}