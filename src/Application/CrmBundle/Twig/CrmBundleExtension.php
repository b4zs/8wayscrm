<?php

namespace Application\CrmBundle\Twig;

use Application\CrmBundle\Enum\ClientStatus;
use Application\CrmBundle\Enum\ProjectStatus;

class CrmBundleExtension extends \Twig_Extension
{
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('crm_get_project_status_icon', function($status){
				$l = ProjectStatus::getAllData();
				return $l[$status]['icon'];
			}),
			new \Twig_SimpleFunction('crm_get_client_status_icon', function($status){
				$l = ClientStatus::getAllData();
				return $l[$status]['icon'];
			}),
			new \Twig_SimpleFunction('crm_get_object_type', function($object){
				$c = get_class($object);
				$c = explode('\\', $c);

				return end($c);
			}),
		);
	}


	public function getName()
	{
		return 'application_crm';
	}
}