<?php
/**
 * ApiCall.php
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
use Nette\Utils;

use IPub;
use IPub\Tumblr;
use IPub\Tumblr\Api;

use IPub\OAuth;

/**
 * Abstract API calls definition
 *
 * @package		iPublikuj:Tumblr!
 * @subpackage	common
 *
 * @author Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class ApiCall extends Nette\Object
{
	/**
	 * @var OAuth\Consumer
	 */
	protected $consumer;

	/**
	 * @var OAuth\HttpClient
	 */
	protected $httpClient;

	/**
	 * @var Configuration
	 */
	protected $config;

	/**
	 * @param OAuth\Consumer $consumer
	 * @param OAuth\HttpClient $httpClient
	 * @param Configuration $config
	 */
	public function __construct(
		OAuth\Consumer $consumer,
		OAuth\HttpClient $httpClient,
		Configuration $config
	){
		$this->consumer = $consumer;
		$this->httpClient = $httpClient;
		$this->config = $config;
	}

	/**
	 * @internal
	 *
	 * @return OAuth\HttpClient
	 */
	public function getHttpClient()
	{
		return $this->httpClient;
	}

	/**
	 * @return Configuration
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param OAuth\Consumer $consumer
	 *
	 * @return $this
	 */
	public function setConsumer(OAuth\Consumer $consumer)
	{
		$this->consumer = $consumer;

		return $this;
	}

	/**
	 * @return OAuth\Consumer
	 */
	public function getConsumer()
	{
		return $this->consumer;
	}

	/**
	 * Determines the access token that should be used for API calls.
	 * The first time this is called, $this->accessToken is set equal
	 * to either a valid user access token, or it's set to the application
	 * access token if a valid user access token wasn't available.  Subsequent
	 * calls return whatever the first call returned.
	 *
	 * @return OAuth\Token The access token
	 */
	abstract public function getAccessToken();

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function get($path, array $params = [], array $headers = [])
	{
		return $this->api($path, Api\Request::GET, $params, [], $headers);
	}

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function head($path, array $params = [], array $headers = [])
	{
		return $this->api($path, Api\Request::HEAD, $params, [], $headers);
	}

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $post
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function post($path, array $params = [], array $post = [], array $headers = [])
	{
		return $this->api($path, Api\Request::POST, $params, $post, $headers);
	}

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $post
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function patch($path, array $params = [], array $post = [], array $headers = [])
	{
		return $this->api($path, Api\Request::PATCH, $params, $post, $headers);
	}

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $post
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function put($path, array $params = [], array $post = [], array $headers = [])
	{
		return $this->api($path, Api\Request::PUT, $params, $post, $headers);
	}

	/**
	 * @param string $path
	 * @param array $params
	 * @param array $headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function delete($path, array $params = [], array $headers = [])
	{
		return $this->api($path, Api\Request::DELETE, $params, [], $headers);
	}

	/**
	 * Simply pass anything starting with a slash and it will call the Api, for example
	 * <code>
	 * $details = $tumblr->api('users/show.json');
	 * </code>
	 *
	 * @param string $path
	 * @param string $method The argument is optional
	 * @param array $params Query parameters
	 * @param array $post Post request parameters or body to send
	 * @param array $headers Http request headers
	 *
	 * @return Utils\ArrayHash|string|Paginator|Utils\ArrayHash[]
	 *
	 * @throws OAuth\Exceptions\ApiException
	 */
	public function api($path, $method = Api\Request::GET, array $params = [], array $post = [], array $headers = [])
	{
		if (is_array($method)) {
			$headers = $post;
			$post = $params;
			$params = $method;
			$method = Api\Request::GET;
		}

		// Check for api key
		if (array_key_exists('api_key', $params)) {
			$params['api_key'] = $this->getConsumer()->getKey();
		}

		$response = $this->httpClient->makeRequest(
			new Api\Request($this->consumer, $this->config->createUrl('api', $path, $params), $method, $post, $headers, $this->getAccessToken()),
			'HMAC-SHA1'
		);

		if (!$response->isJson() || (!$data = Utils\ArrayHash::from($response->toArray()))) {
			$ex = $response->toException();
			throw $ex;
		}

		if ($response->isPaginated()) {
			return new Paginator($this, $response);
		}

		return $data->response;
	}

	/**
	 * Upload photo to the Tumblr
	 *
	 * @param string $blog
	 * @param string $type
	 * @param string $file
	 * @param array $params
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws Exceptions\FileNotFoundException
	 * @throws Exceptions\InvalidArgumentException
	 * @throws OAuth\Exceptions\ApiException|static
	 */
	public function uploadMedia($blog, $type, $file, $params = [])
	{
		if (!file_exists($file)) {
			throw new Exceptions\FileNotFoundException("File '$file' does not exists. Please provide valid path to file.");
		}

		if (!in_array($type, ['photo', 'audio', 'video'])) {
			throw new Exceptions\InvalidArgumentException("File type '$type' is not in allowed type. Please provide valid media type: photo, audio or video.");
		}

		// Add file to post params
		$post = [
			'type' => $type,
			'data' => new \CURLFile($file),
		];

		// Merge default post data with user defined params
		$post = array_merge($post, $params);

		$response = $this->httpClient->makeRequest(
			new Api\Request($this->consumer, $this->config->createUrl('api', 'blog/'. $blog.'/post'), Api\Request::POST, $post, [], $this->getAccessToken()),
			'HMAC-SHA1'
		);

		if ($response->isOk() && $response->isJson() && ($data = Utils\ArrayHash::from($response->toArray()))) {
			return $data;

		} else {
			$ex = $response->toException();
			throw $ex;
		}
	}
}