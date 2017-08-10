<?php


namespace Application\QuotationGeneratorBundle\Tests\Controller;

require 'vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Client;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class FillOutApiControllerTest extends WebTestCase
{

	public function setUp(){

	}

	public function testGetFillouts()
	{
		$client = static::createClient();
		$crawler = $client->request('get', '/fillouts');
		var_dump($crawler->getInfo());
	}
}