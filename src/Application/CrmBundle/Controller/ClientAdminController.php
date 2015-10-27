<?php


namespace Application\CrmBundle\Controller;


use Application\CrmBundle\Admin\ClientAdmin;
use Application\CrmBundle\Enum\ClientType;
use Sonata\AdminBundle\Controller\CRUDController;

class ClientAdminController extends CRUDController
{
	/**
	 * List action.
	 *
	 * @return Response
	 *
	 * @throws AccessDeniedException If access is not granted
	 */
	public function listAction()
	{
		if (false === $this->admin->isGranted('LIST')) {
			throw new AccessDeniedException();
		}

		$datagrid = $this->admin->getDatagrid();
		$formView = $datagrid->getForm()->createView();
		$formViewTop = $datagrid->getForm()->createView();

		// set the theme for the current Admin Form
		$this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

		return $this->render($this->admin->getTemplate('list'), array(
			'action'     => 'list',
			'form'       => $formView,
			'form_top'   => $formViewTop,
			'datagrid'   => $datagrid,
			'csrf_token' => $this->getCsrfToken('sonata.batch'),
		));
	}


}