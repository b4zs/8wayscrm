<?php

namespace Application\RedmineIntegrationBundle\Helper;

class TicketTimeHelper
{
	public function guessEstimatedHoursFromStartDueDates($issueStartDate, $issueDueDate)
	{
		$startDueDiff = $issueDueDate - $issueStartDate;
		$startDueDiffHours = $startDueDiff / (60 * 60) / 3; //shrink 24 hours to 8
		if ($startDueDiffHours < 1) { // probably start date = due date, so let's set 4 hours
			$startDueDiffHours = 4;

		}
		return $startDueDiffHours;
	}

	public function fixIssueDueDatesAndEstimatedHours(array &$issues)
	{
		foreach ($issues as &$issue) {
			$issueStartDate = strtotime($issue['start_date']);
			$estimatedHours = isset($issue['estimated_hours']) ? $issue['estimated_hours'] : 4;

			if (!isset($issue['due_date'])) {
				$issue['due_date'] = date('Y-m-d', strtotime($issue['start_date'].' +'.ceil($estimatedHours*3).'hours'));// multiply by 3, to expand 8 hours to 24
			}

			$issueDueDate = strtotime($issue['due_date']);
			$issue['_width_in_hours'] = $this->guessEstimatedHoursFromStartDueDates($issueStartDate, $issueDueDate);
		}
	}

	public function calculateIssuesMinMaxDates(array &$issues)
	{
		$minStartDate = null;
		$maxDueDate = null;

		foreach ($issues as &$issue) {
			$issueStartDate = strtotime($issue['start_date']);
			$issueDueDate = strtotime($issue['due_date']);

			if (null === $minStartDate || $issueStartDate < $minStartDate) {
				$minStartDate = $issueStartDate;
			}

			if (null === $maxDueDate || $issueDueDate > $maxDueDate) {
				$maxDueDate = $issueDueDate;
			}
		}

		return array($minStartDate, $maxDueDate);
	}

	public function calculateIssuesDistancesFromDate($minStartDate, array &$issues)
	{
		foreach ($issues as &$issue) {
			$issueStartDate = strtotime($issue['start_date']);
			$issue['_distance_from_start_in_days'] = round(($issueStartDate - $minStartDate) / (60*60*24));
		}
	}

	public function shiftIssueStartAndDueDatesByDays(array &$issues, $days)
	{
		foreach ($issues as &$issue) {
			$issue['start_date'] = date('Y-m-d', strtotime($issue['start_date'].' +'.$days.'days'));
			$issue['due_date'] = date('Y-m-d', strtotime($issue['due_date'].' +'.$days.'days'));

			if (empty($issue['_modified'])) {
				$issue['_modified'] = array();
			};
			$issue['_modified'][] = 'start_date';
			$issue['_modified'][] = 'due_date';
			$issue['_modified'] = array_unique($issue['_modified']);
		}
	}

	public function getFirstInProgressTicket(array &$issues)
	{
		//TODO: reorder tickets
		foreach ($issues as &$issue) {
			if ('In progress' === $issue['status']['name']) {
				return $issue;
			}
		}

		return null;
	}

	public function filterModifiedTickets(&$issues)
	{
		$filteredTickets = array();
		foreach ($issues as &$issue) {
			if (!empty($issue['_modified'])) {
				$filteredTickets[] =& $issue;
			}
		}

		return $filteredTickets;
	}
}