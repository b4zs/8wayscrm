<?php


namespace Application\RedmineIntegrationBundle\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends CRUDController
{
	public function fixGanttAction(Request $request)
	{
		if (!$this->isGranted('ROLE_APPLICATION_REDMINE_GANTT_LIST')) {
			throw new AccessDeniedHttpException('Access Denied.');
		}

		if (!$this->get('security.token_storage')->getToken()->getUser()->getRedmineAuthToken()) {
			throw new AccessDeniedHttpException('No redmine authentication token');
		}
		$datagrid = $this->admin->getDatagrid();
		$formView = $datagrid->getForm()->createView();
		$ticketTimeHelper = $this->container->get('application_redmine_integration.helper.ticket_time');
		$communicationHelper = $this->container->get('application_redmine_integration.helper.communication');
		$communicationHelper->setToken($this->getUser()->getRedmineAuthToken());

		$this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

		$data = $communicationHelper->getProjectTickets('timelinefixer-demo');
		$issues =& $data['issues'];

		$ticketTimeHelper->fixIssueDueDatesAndEstimatedHours($issues);
		list($minStartDate, $maxDueDate) = $ticketTimeHelper->calculateIssuesMinMaxDates($issues);
		$ticketTimeHelper->calculateIssuesDistancesFromDate($minStartDate, $issues);
		$ticketTimeHelper->shiftIssueStartAndDueDatesByDays($issues, 2);
		$firstInProgressIssue = $ticketTimeHelper->getFirstInProgressTicket($issues);

		$daysToShift = null;
		if (null !== $firstInProgressIssue) {
			var_dump($firstInProgressIssue['start_date']);die;
			$firstInProgressIssueDistanceFromNow = round(strtotime(date('Y-m-d').' 00:00:00') - strtotime($firstInProgressIssue['start_date']));
			$daysToShift = ($firstInProgressIssueDistanceFromNow / (60*60*24));
//			$daysToShift = 10;
			$ticketTimeHelper->shiftIssueStartAndDueDatesByDays($issues, $daysToShift);

			$communicationHelper->updateTickets($ticketTimeHelper->filterModifiedTickets($issues));

//			die;


//			$communicationHelper->updateTickets

		}


		return $this->render('ApplicationRedmineIntegrationBundle:Project:fixGantt.html.twig', array(
			'action'        => 'list',
			'form'          => $formView,
			'datagrid'      => $datagrid,
			'csrf_token'    => $this->getCsrfToken('sonata.batch'),
			'data'          => $data,
			'min_start'     => $minStartDate,
			'days_shifted'  => $daysToShift,
		));
	}


}