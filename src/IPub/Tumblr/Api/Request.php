<?php
/**
 * Request.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Tumblr!
 * @subpackage	Api
 * @since		5.0
 *
 * @date		26.06.15
 */

namespace IPub\Tumblr\Api;

use IPub;
use IPub\OAuth;

/**
 * Modify API call request parameters and add connection signature to the request
 *
 * @package		iPublikuj:Tumblr!
 * @subpackage	Api
 *
 * @author Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Request extends OAuth\Api\Request
{
	/**
	 * {@inheritdoc}
	 */
	public function isPaginated()
	{
		$params = $this->getParameters();

		return $this->isGet() && (isset($params['limit']) || isset($params['offset']));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSignableParameters()
	{
		// Grab all parameters
		$params = array_merge($this->getParameters(), $this->post);

		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if (isset($params['oauth_signature'])) {
			unset($params['oauth_signature']);
		}

		return OAuth\Utils\Url::buildHttpQuery($params);
	}
}