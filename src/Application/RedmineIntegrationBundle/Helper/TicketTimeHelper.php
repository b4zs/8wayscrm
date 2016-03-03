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
			if (!isset($issue['start_date'])) {//TODO: indicate that it was missing
				$createdOn = strtotime($issue['created_on']);
				$issue['start_date'] = date('Y-m-d', $createdOn);
				$issue['_note']['start_date'] = 'Originally unset, filled with creation date';
			}
			$issueStartDate = strtotime($issue['start_date']);
			$estimatedHours = isset($issue['estimated_hours']) ? $issue['estimated_hours'] : 4;

			if (!isset($issue['due_date'])) {
				$issue['due_date'] = date('Y-m-d', strtotime($issue['start_date'].' +'.ceil($estimatedHours*3).'hours'));// multiply by 3, to expand 8 hours to 24
			}

			$issueDueDate = strtotime($issue['due_date']);
			$issue['_width_in_hours'] = round($this->guessEstimatedHoursFromStartDueDates($issueStartDate, $issueDueDate));
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

	public function calculateIssuesDistancesFromDate($origin, array &$issues)
	{
		$origin = strtotime($origin);
		foreach ($issues as &$issue) {
			$issueStartDate = strtotime($issue['start_date'].' 00:00:00');
			$issue['_distance_in_days'] = round(($issueStartDate - $origin) / (60*60*24));
		}
	}

	public function shiftIssueStartAndDueDatesByDays(array $issues, $days)
	{
		foreach ($issues as &$issue) {
			$startDate = strtotime($issue['start_date'].' +'.$days.'days');
			$dueDate = strtotime($issue['due_date'].' +'.$days.'days');


			if ($this->isWeekend($startDate)) {
				//TODO: ?!
			}

			if (empty($issue['_modified'])) {
				$issue['_modified'] = array();
			};

			if (date('Y-m-d', $startDate) != $issue['start_date']) {
				$issue['_modified']['start_date'] = date('Y-m-d', $startDate);
			}

			if (date('Y-m-d', $dueDate) != $issue['due_date']) {
				$issue['_modified']['due_date'] = date('Y-m-d', $dueDate);
			}
		}
	}

	private function isWeekend($timestamp)
	{
		return in_array(date('w', $timestamp), array(0, 6));
	}

	public function getFirstInProgressTicket(array $issues)
	{
		$this->orderTicketsByStartDate($issues);
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

	public function orderTicketsByStartDate(array &$issues)
	{
		usort($issues, function($a, $b){
			if ($a['start_date'] == $b['start_date']) {
				return $a['id'] < $b['id'] ? -1 : 1;
			} else {
				return (strtotime($a['start_date']) < strtotime($b['start_date'])) ? -1 : 1;
			}
		});
	}
}