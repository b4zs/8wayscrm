<?php
namespace Application\AdminBundle;

use Application\AdminBundle\DependencyInjection\Compiler\ResultProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   bundles : http://symfony.com/doc/current/book/bundles.html
 *
 * @author <yourname> <youremail>
 */
class ApplicationAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ResultProviderCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}