<?php
namespace Application\CrmBundle\Controller;

use Application\CrmBundle\Admin\ClientAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class ClientAdminController
 * @package Application\CrmBundle\Controller
 *
 * @property ClientAdmin $admin
 */
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
			$this->createAccessDeniedException();
		}

        $datagrid = $this->admin->getDatagrid();

        $formView = $datagrid->getForm()->createView();
        $formTop  = $datagrid->getForm()->createView();

		// set the theme for the current Admin Form
		$this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

		return $this->render($this->admin->getTemplate('list'), array(
			'action'     => 'list',
			'form'       => $formView,
			'form_top'       => $formTop,
			'datagrid'   => $datagrid,
			'csrf_token' => $this->getCsrfToken('sonata.batch'),
		));
	}

}