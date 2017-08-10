<?php


namespace Application\QuotationGeneratorBundle\Twig;


class LambdaFunctionExtension extends \Twig_Extension
{
	public function getName() {
		return 'lambda_function';
	}

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('call', array($this, 'doCall'))
		);
	}

	public function doCall() {
		$arguments = func_get_args();
		$callable = array_shift($arguments);
		if(!is_callable($callable)) {
			throw new \InvalidArgumentException();
		}
		return call_user_func_array($callable, $arguments);
	}
}