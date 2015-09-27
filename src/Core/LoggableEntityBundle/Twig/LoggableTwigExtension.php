<?php

namespace Core\LoggableEntityBundle\Twig;

class LoggableTwigExtension extends \Twig_Extension
{
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('is_string', 'is_string'),
		);
	}

	public function getName()
	{
		return 'loggable_twig_ext';
	}


}