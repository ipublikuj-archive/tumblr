<?php
/**
 * Test: IPub\Tumblr\Client
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Tumblr!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		28.03.15
 */

namespace IPubTests\Tumblr;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Tumblr;

require_once __DIR__ . '/TestCase.php';

class ClientTest extends TestCase
{
	public function testUnauthorized()
	{
		$client = $this->buildClient();

		Assert::same(0, $client->getUser());
	}

	public function testAuthorized_savedInSession()
	{
		$client = $this->buildClient();

		$session = $client->getSession();
		$session->access_token = 'abcedf';
		$session->access_token_secret = 'ghijklmn';
		$session->user_id = 123321;

		Assert::same(123321, $client->getUser());
	}

	public function testAuthorized_readUserIdFromAccessToken()
	{
		$client = $this->buildClient();

		$client->setAccessToken([
			'access_token'          => 'abcedf',
			'access_token_secret'   => 'ghijklmn',
		]);

		$this->httpClient->fakeResponse('{"meta": {"status":200,"msg":"OK"},"response":{"user":{"following":263,"default_post_format":"html","name":"derekg"}}}', 200, ['Content-Type' => 'application/json; charset=utf-8']);

		Assert::same('derekg', $client->getUser());
		Assert::count(1, $this->httpClient->requests);

		$secondRequest = $this->httpClient->requests[0];

		Assert::same('GET', $secondRequest->getMethod());
		Assert::match('http://api.tumblr.com/v2/user/info', $secondRequest->getUrl()->getHostUrl() . $secondRequest->getUrl()->getPath());
		Assert::same(['Accept' => 'application/json'], $secondRequest->getHeaders());
	}

	public function testAuthorized_authorizeFromVerifierAndToken()
	{
		$client = $this->buildClient(array('oauth_verifier' => 'abcedf', 'oauth_token' => 'ghijklmn'));

		$this->httpClient->fakeResponse('oauth_token=72157626318069415-087bfc7b5816092c&oauth_token_secret=a202d1f853ec69de', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
		$this->httpClient->fakeResponse('{"meta": {"status":200,"msg":"OK"},"response":{"user":{"following":263,"default_post_format":"html","name":"derekg"}}}', 200, ['Content-Type' => 'application/json; charset=utf-8']);

		Assert::same('derekg', $client->getUser());
		Assert::count(2, $this->httpClient->requests);

		$firstRequest = $this->httpClient->requests[0];

		Assert::same('POST', $firstRequest->getMethod());
		Assert::match('http://www.tumblr.com/oauth/access_token', $firstRequest->getUrl()->getHostUrl() . $firstRequest->getUrl()->getPath());
		Assert::same(['Accept' => 'application/json'], $firstRequest->getHeaders());

		$secondRequest = $this->httpClient->requests[1];

		Assert::same('GET', $secondRequest->getMethod());
		Assert::match('http://api.tumblr.com/v2/user/info', $secondRequest->getUrl()->getHostUrl() . $secondRequest->getUrl()->getPath());
		Assert::same(['Accept' => 'application/json'], $secondRequest->getHeaders());
	}
}

\run(new ClientTest());
