<?php

namespace Application\RedmineIntegrationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GanttController extends CRUDController
{
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_APPLICATION_REDMINE_GANTT_LIST')) {
            throw new AccessDeniedHttpException('Access Denied.');
        }

        if (!$this->get('security.token_storage')->getToken()->getUser()->getRedmineAuthToken()) {
            throw new AccessDeniedHttpException('No redmine authentication token');
        }
        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('ApplicationRedmineIntegrationBundle:Gantt:index.html.twig', array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }
}
