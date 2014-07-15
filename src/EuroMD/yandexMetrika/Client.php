<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\clients\YandexOAuth;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\validators\StringValidator;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 *
 * @property YandexOAuth $apiClient
 */
class Client extends Component
{
	const CODE_UNAUTHORIZED = 401;

	const YANDEX_OT_MIN = 500;
	const YANDEX_OT_MAX = 32000;

	/** @var string */
	public $clientID;
	/** @var string */
	public $clientSecret;
	/** @var YandexOAuth */
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
	 * @return YandexOAuth
	 */
	public function getApiClient()
	{
		if(!$this->_apiClient) {
			$this->_apiClient = new YandexOAuth([
				'clientId' => $this->clientID,
				'clientSecret' => $this->clientSecret,
				'apiBaseUrl' => $this->_apiBaseUrl,
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

		if(!$validator->validate($text, $error)) {
			throw new InvalidParamException($error);
		}

		$text = urlencode("<original-text><content>{$text}</content></original-text>");

		$this->apiClient->setCurlOptions([
			CURLOPT_POSTFIELDS => $text,
		]);

		$headers = [
			$this->getAuthHeader(),
			'Content-Length: ' . mb_strlen($text),
		];

		$response = $this->apiClient->api("hosts/$yandexSiteID/original-texts/", "POST", [], $headers);
		var_dump($response); exit;
		//Dumper::dump($response); exit;

		return true;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		if($code) {
			$this->apiClient->fetchAccessToken($code);
		}
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
		}
		throw new InvalidParamException('NOT VALID ACCESS TOKEN', self::CODE_UNAUTHORIZED);
	}

	/**
	 * Authorization url
	 * @return string
	 */
	public function getAuthorizeURL()
	{
		return $this->apiClient->buildAuthUrl(['display' => 'iframe', 'redirect_uri' => null, 'xoauth_displayname' => null]);
	}
}