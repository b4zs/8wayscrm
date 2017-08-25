    <?php

use Application\AdminBundle\ApplicationAdminBundle;
use Application\MediaBundle\ApplicationMediaBundle;
use Application\UserBundle\ApplicationUserBundle;
use Core\SecurityBundle\CoreSecurityBundle;
use Core\ToolsBundle\CoreToolsBundle;
use FOS\RestBundle\FOSRestBundle;
use FOS\UserBundle\FOSUserBundle;
use Gedmo\DoctrineExtensions;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Octet\Ticketing\Bundle\OctetTicketingBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\CoreBundle\SonataCoreBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\UserBundle\SonataUserBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
	        new KnpMenuBundle(),
            new JMSSerializerBundle(),
            new FOSRestBundle(),
	        new SonataCoreBundle(),
			new SonataAdminBundle(),
	        new SonataBlockBundle(),
	        new SonataDoctrineORMAdminBundle(),
            new Application\ProjectAccountingBundle\ApplicationProjectAccountingBundle(),
            new Application\CrmBundle\ApplicationCrmBundle(),
//            new CoreSecurityBundle(),
//            new CoreToolsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            ///media
            // ...
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new ApplicationMediaBundle(),

            new FOSUserBundle(),
            new SonataUserBundle(),
            new ApplicationUserBundle(),

            new ApplicationAdminBundle(),
            new Core\LoggableEntityBundle\CoreLoggableEntityBundle(),

            new Core\ToolsBundle\CoreToolsBundle(),
            new Octet\MessageBusBundle\OctetMessageBusBundle(),
            new SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle(),
            new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
            new OctetTicketingBundle(),

            new Core\ObjectIdentityBundle\CoreObjectIdentityBundle(),
            new Application\ObjectIdentityBundle\ApplicationObjectIdentityBundle(),
            new Application\RedmineIntegrationBundle\ApplicationRedmineIntegrationBundle(),
            new Application\ClassificationBundle\ApplicationClassificationBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Application\QuotationGeneratorBundle\ApplicationQuotationGeneratorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
