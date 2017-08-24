<?php


namespace Application\QuotationGeneratorBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	public function indexAction()
	{
		$securityContext = $this->container->get('security.context');
		if ($securityContext->isGranted('ROLE_ADMIN')) {
			return new RedirectResponse($this->container->get('router')->generate('sonata_admin_dashboard'));
	 	} else {
			return new Response('<a href="admin">admin</a>');
		}
	}

	public function dumpAction()
	{
		$questions = $this->container->get('doctrine.orm.default_entity_manager')->getRepository('ApplicationQuotationGeneratorBundle:Question')->findAll();

		return $this->renderView('ApplicationQuotationGeneratorBundle::dump.html.twig', array('questions' => $questions));
	}
}