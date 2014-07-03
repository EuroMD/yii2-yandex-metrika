<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\clients\YandexOAuth;
use yii\helpers\ArrayHelper;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 */
class OAuth2 extends YandexOAuth
{
	/**
	 * @param string $method
	 * @param string $url
	 * @param array  $params
	 * @return array
	 */
	protected function composeRequestCurlOptions($method, $url, array $params)
	{
		$options = parent::composeRequestCurlOptions($method, $url, $params);
		if($this->accessToken->isValid) {
			$options[CURLOPT_HTTPHEADER] = ArrayHelper::merge($options[CURLOPT_HTTPHEADER], [
				'Authorization: OAuth ' . $this->accessToken->token
			]);
		}
		return $options;
	}
} 