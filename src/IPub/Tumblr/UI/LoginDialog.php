<?php
/**
 * LoginDialog.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Tumblr!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		26.06.15
 */

namespace IPub\Tumblr\UI;

use Nette;
use Nette\Application;
use Nette\Http;

use IPub;
use IPub\Tumblr;
use IPub\Tumblr\Exceptions;

use IPub\OAuth;

/**
 * Component that you can connect to presenter
 * and use as public mediator for Tumblr OAuth redirects communication
 *
 * @package		iPublikuj:Tumblr!
 * @subpackage	UI
 *
 * @method onResponse(LoginDialog $dialog)
 */
class LoginDialog extends Application\UI\Control
{
	/**
	 * @var array of function(LoginDialog $dialog)
	 */
	public $onResponse = [];

	/**
	 * @var Tumblr\Client
	 */
	protected $client;

	/**
	 * @var Tumblr\Configuration
	 */
	protected $config;

	/**
	 * @var Tumblr\SessionStorage
	 */
	protected $session;

	/**
	 * @param Tumblr\Client $tumblr
	 */
	public function __construct(Tumblr\Client $tumblr)
	{
		$this->client = $tumblr;
		$this->config = $tumblr->getConfig();
		$this->session = $tumblr->getSession();

		parent::__construct();

		$this->monitor('Nette\Application\IPresenter');
	}

	/**
	 * @return Tumblr\Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param Nette\ComponentModel\Container $obj
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if ($obj instanceof Nette\Application\IPresenter) {
			$this->client->getConsumer()->setCallbackUrl(new Http\UrlScript($this->link('//response!')));
		}
	}

	public function handleCallback()
	{

	}

	/**
	 * Checks, if there is a user in storage and if not, it redirects to login dialog.
	 * If the user is already in session storage, it will behave, as if were redirected from tumblr right now,
	 * this means, it will directly call onResponse event.
	 *
	 * @throws Nette\Application\AbortException
	 */
	public function handleOpen()
	{
		if (!$this->client->getUser()) { // no user
			$this->open();
		}

		$this->onResponse($this);
		$this->presenter->redirect('this');
	}

	/**
	 * @throws Nette\Application\AbortException
	 * @throws OAuth\Exceptions\RequestFailedException
	 */
	public function open()
	{
		if ($this->client->obtainRequestToken()) {
			$this->presenter->redirectUrl($this->getUrl());

		} else {
			throw new OAuth\Exceptions\RequestFailedException(sprintf('User could not be authenticated to "%s".', 'tumblr'));
		}
	}

	/**
	 * @return array
	 */
	public function getQueryParams()
	{
		$params = [
			'oauth_token' => $this->session->request_token
		];

		return $params;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return (string) $this->config->createUrl('oauth', 'authorize', $this->getQueryParams());
	}

	public function handleResponse()
	{
		$this->client->getUser(); // check the received parameters and save user
		$this->onResponse($this);
		$this->presenter->redirect('this', ['oauth_token' => NULL, 'oauth_verifier' => NULL]);
	}
}