<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\OAuth2;
use yii\base\Component;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 */
class Client extends Component
{
	/** @var OAuth2 */
	private $apiClient;
	/** @var string Yandex API base URL */
	private $apiBaseUrl = 'https://webmaster.yandex.ru/api/v2/';

	public function init()
	{
		parent::init();
		$this->apiClient = new OAuth2();
		$this->apiClient->apiBaseUrl = $this->apiBaseUrl;
	}

	/**
	 * Add Original Text
	 * @param string $text
	 * @param int $siteID
	 */
	public function addOriginalText($text, $siteID)
	{
		$text = urlencode($text);
		$this->apiClient->api("hosts/$siteID/original-texts/", "POST", $text);
	}
}