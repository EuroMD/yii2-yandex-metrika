<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\base\Component;
use yii\base\InvalidParamException;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 *
 * @property OAuth2 $apiClient
 */
class Client extends Component
{
	/** @var string */
	public $clientID;
	/** @var string */
	public $clientSecret;

	/** @var OAuth2 */
	private $_apiClient;
	/** @var string Yandex API base URL */
	private $_apiBaseUrl = 'https://webmaster.yandex.ru/api/v2/';

	public function init()
	{
		parent::init();
		if(!$this->clientID || !$this->clientSecret)
			throw new InvalidParamException;
	}

	/**
	 * API Client
	 * @return OAuth2
	 */
	public function getApiClient()
	{
		if(!$this->_apiClient) {
			$this->_apiClient = new OAuth2([
				'clientId' => $this->clientID,
				'clientSecret' => $this->clientSecret,
				'apiBaseUrl' => $this->_apiBaseUrl
			]);
		}
		return $this->_apiClient;
	}

	/**
	 * Add Original Text
	 * @param string $text
	 * @param int    $siteID
	 * @throws InvalidParamException
	 * @return array
	 */
	public function addOriginalText($text, $siteID)
	{
		$text = urlencode($text);
		return $this->apiClient->api("hosts/$siteID/original-texts/", "POST", [$text]);
	}
}