<?php


namespace Application\RedmineIntegrationBundle\Helper;


use Buzz\Browser;

class CommunicationHelper
{
	private $redmineDomain;

	private $token;

	/** @var  Browser */
	private $browser;

	public function getProjectTickets($projectId)
	{
//		$format = 'xml';
		$format = 'json';
		$url = $this->redmineDomain;
		$url .= '/issues.'.$format;
		$url .= '?' . http_build_query(array(
			'project_id' => $projectId,
			'limit' => 200,
			'key' => $this->token,
		));

		$browser = $this->browser->get($url);
		$content = $browser->getContent();
		$data = json_decode($content, true);

		return $data['issues'];
	}

	public function updateTickets(array $issues)
	{
		foreach ($issues as &$issue) {
			$url = $this->redmineDomain;
			$url .= '/issues/'.$issue['id'].'.json';
			$url .= '?' . http_build_query(array(
				'key' => $this->token,
			));
			$requestBody = json_encode(array(
				'issue' => $this->buildUpdateIssueRequestData($issue)
			));
			$headers = array(
				'Content-Type' => 'application/json',
			);

			/** @var \Buzz\Message\Response $response */
			$response = $this->browser->put($url, $headers, $requestBody);
			if (200 !== $response->getStatusCode()) {
				throw new \RuntimeException($response->getContent());
			}
		}

	}

	public function setRedmineDomain($redmineDomain)
	{
		$this->redmineDomain = $redmineDomain;
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function setBrowser(Browser $browser)
	{
		$this->browser = $browser;
	}

	private function buildUpdateIssueRequestData($issue)
	{
		$result = array();
		foreach ($issue['_modified'] as $field => $value) {
			$result[$field] = $value;
		}

		return $result;
	}

	public function fetchAllProjects()
	{
		$offset = 0;
		$limit = 100;
		$projects = array();

		while (true) {
			$url = $this->redmineDomain;
			$url .= '/projects.json';
			$url .= '?' . http_build_query(array(
				'key' => $this->token,
				'limit'     => $limit,
				'offset'    => $offset,
			));

			/** @var \Buzz\Message\Response $response */
			$response = $this->browser->get($url, array());
			$data = json_decode($response->getContent(), true);
			$totalCount = $data['total_count'];
			$projects = array_merge($projects, $data['projects']);

			if ($totalCount < ($offset + $limit)) {
				break;
			} else {
				$offset += $limit;
			}
		}

//		var_dump($projects);die;
		return $projects;
	}
}