<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\clients\YandexOAuth;
use yii\caching\Cache;

/**
 * @package EuroMD\yandexMetrika
 * @author Borales
 */
class OAuth2 extends YandexOAuth
{
	/** @var string */
	public $cacheComponent = 'cache';
	/** @var string Yandex API base URL */
	public $apiBaseUrl = 'https://webmaster.yandex.ru/api/v2';

	public function api($apiSubUrl, $method = 'GET', $params = [], array $headers = [])
	{
		return parent::api($apiSubUrl, $method, $params, $headers);
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this|static
	 */
	protected function setState($key, $value)
	{
		$key = $this->getStateKeyPrefix() . $key;
		$duration = $this->getCacheDuration();
		$this->getCache()->set($key, $value, $duration);
		return $this;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	protected function getState($key)
	{
		$key = $this->getStateKeyPrefix() . $key;
		return $this->getCache()->get($key);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	protected function removeState($key)
	{
		$key = $this->getStateKeyPrefix() . $key;
		$this->getCache()->delete($key);
		return true;
	}

	/**
	 * @return Cache
	 */
	private function getCache()
	{
		return \Yii::$app->get($this->cacheComponent);
	}

	/**
	 * @return int
	 */
	private function getCacheDuration()
	{
		return 24 * 60 * 60 * 180; // 180 days
	}
}