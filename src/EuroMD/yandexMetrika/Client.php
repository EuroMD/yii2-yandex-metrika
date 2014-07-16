<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\validators\StringValidator;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 *
 * @property OAuth2 $apiClient
 */
class Client extends Component
{
	const CODE_UNAUTHORIZED = 401;

	const YANDEX_OT_MIN = 500;
	const YANDEX_OT_MAX = 32000;

	/** @var string */
	public $cacheComponent = 'cache';
	/** @var string */
	public $clientID;
	/** @var string */
	public $clientSecret;
	/** @var OAuth2 */
	private $_apiClient;

	public function init()
	{
		parent::init();
		if (!$this->clientID || !$this->clientSecret)
			throw new InvalidParamException;
	}

	/**
	 * API Client
	 * @return OAuth2
	 */
	public function getApiClient()
	{
		if (!$this->_apiClient) {
			$this->_apiClient = new OAuth2([
				'clientId' => $this->clientID,
				'clientSecret' => $this->clientSecret,
				'cacheComponent' => $this->cacheComponent,
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

		if (!$validator->validate($text, $error)) {
			throw new InvalidParamException($error);
		}

		$text = urlencode(htmlspecialchars($text));
		$text = "<original-text><content>{$text}</content></original-text>";

		try {
			$response = $this->apiClient->api("hosts/$yandexSiteID/original-texts/", "POST", $text);
			VarDumper::dump($response);
		} catch (\Exception $e) {
			VarDumper::dump($e->getMessage());
		}

		exit;
		//$response = \Requests::post($this->apiClient->apiBaseUrl . "/hosts/$yandexSiteID/original-texts/", $headers, $text);
		if ($response->success) {
			$body = $response->body;
			if ($body) {
				$body = (array)simplexml_load_string($body);
				if (ArrayHelper::keyExists('id', $body) && ArrayHelper::keyExists('link', $body)) {
					return true;
				}
			}
		}

		$msg = 'Yandex API Error';
		if ($response->body) {
			$msg = trim(strip_tags($response->body));
		}
		throw new InvalidParamException($msg);
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->apiClient->fetchAccessToken($code);
	}

	/**
	 * Auth header
	 * @return array
	 * @throws InvalidParamException
	 */
	protected function getAuthHeader()
	{
		if ($this->apiClient->accessToken && $this->apiClient->accessToken->isValid) {
			return 'Authorization: OAuth ' . $this->apiClient->accessToken->token;
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