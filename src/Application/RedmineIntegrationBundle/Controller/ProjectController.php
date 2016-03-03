<?php


namespace Application\RedmineIntegrationBundle\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProjectController extends CRUDController
{
	const FORM_NAME = 'a';

	public function ganttFixerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_REDMINE_PROJECT_GANTT_FIXER')) {
			throw new AccessDeniedHttpException('Access Denied.');
		}

		if (!$this->get('security.token_storage')->getToken()->getUser()->getRedmineAuthToken()) {
			throw new AccessDeniedHttpException('No redmine authentication token');
		}
		$datagrid = null;
		$issues = array();
		$projectId = null;
		$ticketTimeHelper = $this->getTicketTimeHelper();
		$communicationHelper = $this->getCommunicationHelper();

		$formBuilder = $this->buildActionFrom(self::FORM_NAME);
		$formData = $request->get(self::FORM_NAME);

		if (!empty($formData['project'])) {
			$projectId = $formData['project'];
			$issues = $this->getCommunicationHelper()->getProjectTickets($projectId);
			$ticketTimeHelper->fixIssueDueDatesAndEstimatedHours($issues);
			$ticketTimeHelper->calculateIssuesDistancesFromDate(date('Y-m-d'). ' 00:00:00', $issues);

			if (!isset($formData['shift_reference_to'])) {
				$referenceTicket = $this->calculateReferenceTicket($issues);
				$formData['shift_reference_to'] = $referenceTicket && $referenceTicket['start_date']
					? $referenceTicket['start_date']
					: date('Y-m-d');
			}
		};


		$this->updateFormAccordingToRequestData($formBuilder, $request, $issues);


		$form = $formBuilder->getForm();
		$form->submit($formData);
		if ($form->isSubmitted()) {
			if (!$form->isValid()) {
//				var_dump($formData, $request->query->all());
				var_dump($form->getErrorsAsString());die;
			} else {
				$formData = $form->getData();
				if ($projectId) {
					$referenceTicket = $this->calculateReferenceTicket($issues);

					if ($form->get('mark_all')->isClicked()) {
						$formData['selected_issue'] = array_keys($this->extractTicketIds($issues));
						return $this->reloadWithNewFormData($request, $formData, $form);
					}
					if ($form->get('mark_none')->isClicked()) {
						$formData['selected_issue'] = array();
						return $this->reloadWithNewFormData($request, $formData, $form);
					}
					if ($form->get('mark_remaining')->isClicked()) {
						$formData['selected_issue'] = $this->extractRemainingTicketIds($issues);
						return $this->reloadWithNewFormData($request, $formData, $form);
					}
					if (!empty($formData['shift_reference_to']) && $formData['shift_reference_to'] instanceof \DateTime) {
						$shiftReferenceToFormatted = $formData['shift_reference_to']->format('Y-m-d').' 00:00:00';
						$referenceTicketDistanceFromNow = round(strtotime($shiftReferenceToFormatted) - strtotime($referenceTicket['start_date']));
						$daysToShift = ($referenceTicketDistanceFromNow / (60*60*24));
						if ($daysToShift) {
							$selectedTickets = $this->filterSelectedTickets($issues, $formData['selected_issue']);
							if (0 === count($selectedTickets)) {
								$request->getSession()->getFlashbag()->add('warning', '0 ticket selected');
							}

							$ticketTimeHelper->shiftIssueStartAndDueDatesByDays($selectedTickets, $daysToShift);

							if ($form->get('shift_reference_do')->isClicked()) {
								$modifiedTickets = $ticketTimeHelper->filterModifiedTickets($issues);
								$communicationHelper->updateTickets($modifiedTickets);
								$request->getSession()->getFlashbag()->add('success', count($modifiedTickets).' ticket(s) has been updated');
								unset($formData['shift_reference_do']);
								return $this->reloadWithNewFormData($request, $formData, $form);
							}
						}
					}
				}
			}
		}

		$formData = $form->getData();
		list($minStartDate, $maxDueDate) = $ticketTimeHelper->calculateIssuesMinMaxDates($issues);
		$formView = $form->createView();

		return $this->render('ApplicationRedmineIntegrationBundle:Project:fixGantt.html.twig', array(
			'action'            => 'list',
			'datagrid'          => $datagrid,
			'csrf_token'        => $this->getCsrfToken('sonata.batch'),
			'issues'            => isset($issues) ? $issues : null,
			'min_start'         => isset($minStartDate) ? $minStartDate : 0,
			'max_due'           => isset($maxDueDate) ? $maxDueDate : 0,
			'days_shifted'      => isset($daysToShift) ? $daysToShift : null,
			'redmine_base_url'  => $this->container->getParameter('application_redmine_integration.redmine_api_url'),
			'reference_ticket'  => isset($referenceTicket) ? $referenceTicket : null,
			'form'              => $formView,
			'issue_checkboxes'  => $this->indexIssueCheckboxes($formView),
			'form_data'         => $formData,
		));
	}


	private function buildActionFrom($formName)
	{
		$formBuilder = $this->container->get('form.factory')
			->createNamedBuilder($formName, 'form', array(), array(
				'method'            => 'GET',
				'csrf_protection'   => false,
				'data_class'        => null,
				'allow_extra_fields'=> true,
			))
			->add('project', 'choice', array(
				'choices'   => $this->getProjects(),
				'required'  => false,
			))
			->add('reload', 'submit', $this->buildActionButtonOptions(array('label' => 'Reload tickets')))
		;

		return $formBuilder;
	}

	private function updateFormAccordingToRequestData(FormBuilder $formBuilder, Request $request, array &$issues)
	{
		$formData = $request->get(self::FORM_NAME);
		$projectId = isset($formData['project']) ? $formData['project'] : null;
		if ($projectId) {
			$formBuilder->add('mark_all', 'submit', $this->buildActionButtonOptions());
			$formBuilder->add('mark_none', 'submit', $this->buildActionButtonOptions());
			$formBuilder->add('mark_remaining', 'submit', $this->buildActionButtonOptions());

			$this->getTicketTimeHelper()->orderTicketsByStartDate($issues);
			$referenceTicket = $this->calculateReferenceTicket($issues);

			$formBuilder->add('selected_issue', 'choice', array(
				'choices' => $this->extractTicketIds($issues),
				'multiple'=> true,
				'expanded'=> true,
			));

			if ($referenceTicket) {
				$formBuilder->add('shift_reference_to', 'sonata_type_date_picker', array(
					'required' => false,
					'datepicker_use_button' => false,
					'format'    => DateType::HTML5_FORMAT,
				));
				if (!isset($formData['shift_reference_to'])) {
					$formData['shift_reference_to'] = $referenceTicket['start_date'];
				}
				$formBuilder->add('shift_reference_preview', 'submit', $this->buildActionButtonOptions(array('label' => 'Preview')));
				$formBuilder->add('shift_reference_do', 'submit', $this->buildActionButtonOptions(array('label' => 'Update')));
			}
		}
	}

	private function getProjects($invalidateCache = false)
	{
		$communicationHelper = $this->getCommunicationHelper();
		$cacheKey = 'redmine_all_projects_choices';
		$refresh = function()use($communicationHelper) {
			$projects = $communicationHelper->fetchAllProjects();
			$result = array();
			foreach ($projects as $project) {
				$result[(string)$project['id']] = $project['name'];
			}

			return $result;
		};

		$data = $this->getCache()->fetch($cacheKey);
		if (!$data || $invalidateCache) {
			$data = $refresh();
			$this->getCache()->save($cacheKey, $data, 3600);
		}

		$r = array();
		foreach ($data as $key => $value) {
			$r[(string)$key] = $value;
		}

		return $r;
	}

	protected function getCommunicationHelper()
	{
		$communicationHelper = $this->container->get('application_redmine_integration.helper.communication');
		$communicationHelper->setToken($this->getUser()->getRedmineAuthToken());

		return $communicationHelper;
	}

	protected function getTicketTimeHelper()
	{
		$ticketTimeHelper = $this->container->get('application_redmine_integration.helper.ticket_time');
		return $ticketTimeHelper;
	}

	private function extractTicketIds(array $issues)
	{
		$ids = array();
		foreach ($issues as $issue) {
			$ids[$issue['id']] = $issue['subject'];
		}

		return $ids;
	}

	private function reloadWithNewFormData(Request $request, array $formData, Form $form)
	{
		foreach ($formData as $k => $v) {
			if ($v instanceof \DateTime) {
				$formData[$k] = $form->createView()->offsetGet($k)->vars['value'];
				$formData[$k] = $v->format('Y-m-d');
			}
		}

		$query = $request->query->all();
		$query[$form->getName()] = $formData;
//		echo '<pre>';var_dump($query);die;
		$url = $this->container->get('router')->generate($request->get('_route'), $query);
		return new RedirectResponse($url);
	}

	private function getCache()
	{
		return $this->container->get('common_cache');
	}

	private function indexIssueCheckboxes(FormView $formView)
	{
		if (!$formView->offsetExists('selected_issue')) {
			return array();
		}

		$issueCheckboxes = $formView->offsetGet('selected_issue');
		$result = array();
		foreach ($issueCheckboxes as $issueCheckbox) {
			$result[$issueCheckbox->vars['value']] = $issueCheckbox;
		}

		return $result;
	}

	public function extractRemainingTicketIds(array $tickets)
	{
		$firstInProgressIssue = $this->getTicketTimeHelper()->getFirstInProgressTicket($tickets);
		if (count($firstInProgressIssue) === 0) {
			return array();
		}

		$result = array();
		$reachedInProgress = false;
		foreach ($tickets as $ticket) {
			if ($ticket['id'] == $firstInProgressIssue['id']) {
				$reachedInProgress = true;
			}
			if ($reachedInProgress) {
				$result[] = $ticket['id'];
			}
		}

		return $result;
	}

	private function buildActionButtonOptions(array $options = array())
	{
		return array_merge(array(
			'attr' => array(
				'onclick' => 'this.form.submit()',
			)
		), $options);
	}

	public function calculateReferenceTicket(array $issues)
	{
		$ticketTimeHelper = $this->getTicketTimeHelper();
		if ($firstInProgressTicket = $ticketTimeHelper->getFirstInProgressTicket($issues)) {
			return $firstInProgressTicket;
		} else {
			return reset($issues);
		}
	}

	private function filterSelectedTickets(array &$issues, array $selectedTicketIds)
	{
		$result = array();
		foreach ($issues as &$issue) {
			if (in_array($issue['id'], $selectedTicketIds)) {
				$result[] =& $issue;
			}
		}

		return $result;
	}

}