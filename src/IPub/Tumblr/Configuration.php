<?php
/**
 * Configuration.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Tumblr!
 * @subpackage	common
 * @since		5.0
 *
 * @date		26.06.15
 */

namespace IPub\Tumblr;

use Nette;
use Nette\Http;

/**
 * Tumblr's extension configuration storage. Store basic extension settings
 *
 * @package		iPublikuj:Tumblr!
 * @subpackage	common
 *
 * @author Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Configuration extends Nette\Object
{
	const CLASSNAME = __CLASS__;

	/**
	 * @var string
	 */
	public $consumerKey;

	/**
	 * @var string
	 */
	public $consumerSecret;

	/**
	 * @var array
	 */
	public $domains = [
		'oauth' => 'http://www.tumblr.com/oauth/',
		'api' => 'http://api.tumblr.com/v2/',
	];

	/**
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 */
	public function __construct($consumerKey, $consumerSecret)
	{
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * Build the URL for given domain alias, path and parameters.
	 *
	 * @param string $name The name of the domain
	 * @param string $path Optional path (without a leading slash)
	 * @param array $params Optional query parameters
	 *
	 * @return Http\UrlScript The URL for the given parameters
	 */
	public function createUrl($name, $path = NULL, $params = [])
	{
		if (preg_match('~^https?://([^.]+\\.)?tumblr\\.com/~', trim($path))) {
			$url = new Http\UrlScript($path);

		} else {
			$url = new Http\UrlScript($this->domains[$name]);
			$path = $url->getPath() . ltrim($path, '/');
			$url->setPath($path);
		}

		$url->appendQuery(array_map(function ($param) {
			return $param instanceof Http\UrlScript ? (string) $param : $param;
		}, $params));

		return $url;
	}

	/**
	 * @param string $consumerKey
	 *
	 * @return $this
	 */
	public function setConsumerKey($consumerKey)
	{
		$this->consumerKey = (string) $consumerKey;

		return $this;
	}

	/**
	 * @param string $consumerSecret
	 *
	 * @return $this
	 */
	public function setConsumerSecret($consumerSecret)
	{
		$this->consumerSecret = (string) $consumerSecret;

		return $this;
	}
}