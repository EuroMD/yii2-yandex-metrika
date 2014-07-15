<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\OAuth2;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\validators\StringValidator;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 *
 * @property OAuth2 $apiClient
 */
class Client extends Component
{
	const YANDEX_OT_MIN = 500;
	const YANDEX_OT_MAX = 32000;

	/** @var string */
	public $clientID;
	/** @var string */
	public $clientSecret;
	/** @var OAuth2 */
	private $_apiClient;
	/** @var string Yandex API base URL */
	private $_apiBaseUrl = 'https://webmaster.yandex.ru/api/v2/';
	/** @var string Yandex API Auth URL */
	private $_authUrl = 'https://oauth.yandex.ru/authorize';

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
				'apiBaseUrl' => $this->_apiBaseUrl,
				'authUrl' => $this->_authUrl,
			]);
		}
		return $this->_apiClient;
	}

	/**
	 * Add Original Text
	 * @param string $text
	 * @param int $yandexSiteID
	 * @throws InvalidParamException
	 * @return string|bool
	 */
	public function addOriginalText($text, $yandexSiteID)
	{
		$validator = new StringValidator([
			'min' => self::YANDEX_OT_MIN,
			'max' => self::YANDEX_OT_MAX,
			'enableClientValidation' => false,
		]);

		$text = urlencode($text);
		if(!$validator->validate($text, $error)) {
			throw new InvalidParamException($error);
		}

		$text = urlencode("<original-text><content>{$text}</content></original-text>");
		$response = $this->apiClient->api("hosts/$yandexSiteID/original-texts/", "POST", [$text], $this->getAuthHeader());
		var_dump($response); exit;
		//Dumper::dump($response); exit;

		return true;
	}

	/**
	 * Auth header
	 * @return array
	 * @throws InvalidParamException
	 */
	protected function getAuthHeader()
	{
		if($this->apiClient->accessToken && $this->apiClient->accessToken->isValid) {
			return [
				'Authorization: OAuth ' . $this->apiClient->accessToken->token
			];
		} else {
			$authUrl = $this->apiClient->apiBaseUrl . $this->apiClient->buildAuthUrl(); // Build authorization URL
			\Yii::$app->getResponse()->redirect($authUrl)->send(); // Redirect to authorization URL.
			//$code = $_GET['code'];
			//$accessToken = $oauthClient->fetchAccessToken($code); // Get access token
			//$authUrl = $this->apiClient->apiBaseUrl . $this->apiClient->buildAuthUrl();
		}

		throw new InvalidParamException('NOT VALID ACCESS TOKEN');
	}
}