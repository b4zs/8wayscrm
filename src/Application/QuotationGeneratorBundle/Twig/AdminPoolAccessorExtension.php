<?php


namespace Application\QuotationGeneratorBundle\Twig;


use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminPoolAccessorExtension extends \Twig_Extension
{
	/**
	 * @var
	 */
	private $container;

	public function __construct(ContainerInterface $container){

		$this->container = $container;
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'core_admin_pool_accessor';
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('admin_get_pool', array($this, 'admin_get_pool')),
			new \Twig_SimpleFunction('resolve_enum', array($this, 'resolveEnum')),
		);
	}

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('resolve_enum', array($this, 'resolveEnum')),
			new \Twig_SimpleFilter('get_class', 'get_class'),
			new \Twig_SimpleFilter('yaml_dump', array('Symfony\\Component\\Yaml\\Yaml', 'dump')),
		);
	}


	/**
	 * @return \Sonata\AdminBundle\Admin\Pool
	 */
	public function admin_get_pool()
	{
		return $this->container->get('sonata.admin.pool');
	}

	public function resolveEnum($value, $enumClass)
	{
		if (!class_exists($enumClass)) {
			throw new \RuntimeException('Enum class does not exists: '.$enumClass);
		}

		$choices = $enumClass::getChoices();

		return isset($choices[$value]) ? $choices[$value] : $value;
	}

}