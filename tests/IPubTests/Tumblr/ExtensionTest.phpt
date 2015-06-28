<?php
/**
 * Test: IPub\Tumblr\Extension
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

require __DIR__ . '/../bootstrap.php';

class ExtensionTest extends Tester\TestCase
{
	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Tumblr\DI\TumblrExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);

		return $config->createContainer();
	}

	public function testCompilersServices()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('Tumblr.client') instanceof IPub\Tumblr\Client);
		Assert::true($dic->getService('Tumblr.config') instanceof IPub\Tumblr\Configuration);
		Assert::true($dic->getService('Tumblr.session') instanceof IPub\Tumblr\SessionStorage);
	}
}

\run(new ExtensionTest());