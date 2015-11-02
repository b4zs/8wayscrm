<?php


namespace Application\CrmBundle\Enum;


use Sonata\IntlBundle\SonataIntlBundle;
use Symfony\Component\Intl\Intl;

class Country
{
	public static function getChoices()
	{
		return Intl::getRegionBundle()->getCountryNames();
	}

	public static function getPreferredChoices()
	{
		return array('CH', 'FR', 'GB');
	}

}