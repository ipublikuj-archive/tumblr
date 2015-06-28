<?php
/**
 * Profile.php
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
use IPub\Tumblr\Exceptions;

/**
 * Tumblr's user profile
 *
 * @package		iPublikuj:Tumblr!
 * @subpackage	common
 *
 * @author Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Profile extends Nette\Object
{
	/**
	 * @var Client
	 */
	private $tumblr;

	/**
	 * @var Utils\ArrayHash
	 */
	private $details;

	/**
	 * @param Client $tumblr
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function __construct(Client $tumblr)
	{
		$this->tumblr = $tumblr;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->tumblr->getUser();
	}

	/**
	 * @param string $key
	 *
	 * @return Utils\ArrayHash|NULL
	 */
	public function getDetails($key = NULL)
	{
		if ($this->details === NULL) {
			try {

				if ($user = $this->tumblr->getUser()) {
					if (($result = $this->tumblr->get('user/info')) && ($result instanceof Utils\ArrayHash)) {
						$this->details = $result->user;
					}

				} else {
					$this->details = new Utils\ArrayHash;
				}

			} catch (\Exception $e) {
				// todo: log?
			}
		}

		if ($key !== NULL) {
			return isset($this->details[$key]) ? $this->details[$key] : NULL;
		}

		return $this->details;
	}
}