<?php
/**
 * Test: IPub\Tumblr\Configuration
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

require_once __DIR__ . '/../bootstrap.php';

class ConfigurationTest extends Tester\TestCase
{
	/**
	 * @var Tumblr\Configuration
	 */
	private $config;

	protected function setUp()
	{
		$this->config = new Tumblr\Configuration('123', 'abc');
	}

	public function testCreateUrl()
	{
		Assert::match('http://api.tumblr.com/v2/user/info', (string) $this->config->createUrl('api', 'user/info'));

		Assert::match('http://www.tumblr.com/oauth/access_token?oauth_consumer_key=123&oauth_signature_method=HMAC-SHA1', (string) $this->config->createUrl('oauth', 'access_token', array(
			'oauth_consumer_key' => $this->config->consumerKey,
			'oauth_signature_method' => 'HMAC-SHA1'
		)));

		Assert::match('http://www.tumblr.com/oauth/request_token?oauth_consumer_key=123&oauth_signature_method=HMAC-SHA1', (string) $this->config->createUrl('oauth', 'request_token', array(
			'oauth_consumer_key' => $this->config->consumerKey,
			'oauth_signature_method' => 'HMAC-SHA1'
		)));
	}
}

\run(new ConfigurationTest());