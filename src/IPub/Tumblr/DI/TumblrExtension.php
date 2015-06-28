<?php
/**
 * TumblrExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Tumblr!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		26.06.15
 */

namespace IPub\Tumblr\DI;

use Nette;
use Nette\DI;
use Nette\Utils;
use Nette\PhpGenerator as Code;

use Tracy;

use IPub;
use IPub\Tumblr;

class TumblrExtension extends DI\CompilerExtension
{
	/**
	 * Extension default configuration
	 *
	 * @var array
	 */
	protected $defaults = [
		'consumerKey' => NULL,
		'consumerSecret' => NULL,
		'clearAllWithLogout' => TRUE
	];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Utils\Validators::assert($config['consumerKey'], 'string', 'Application key');
		Utils\Validators::assert($config['consumerSecret'], 'string', 'Application secret');

		// Create oAuth consumer
		$consumer = new IPub\OAuth\Consumer($config['consumerKey'], $config['consumerSecret']);

		$builder->addDefinition($this->prefix('client'))
			->setClass(Tumblr\Client::CLASSNAME, [$consumer]);

		$builder->addDefinition($this->prefix('config'))
			->setClass(Tumblr\Configuration::CLASSNAME, [
				$config['consumerKey'],
				$config['consumerSecret'],
			]);

		$builder->addDefinition($this->prefix('session'))
			->setClass(Tumblr\SessionStorage::CLASSNAME);

		if ($config['clearAllWithLogout']) {
			$builder->getDefinition('user')
				->addSetup('$sl = ?; ?->onLoggedOut[] = function () use ($sl) { $sl->getService(?)->clearAll(); }', [
					'@container', '@self', $this->prefix('session')
				]);
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'tumblr')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new TumblrExtension());
		};
	}
}