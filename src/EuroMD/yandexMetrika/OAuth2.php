<?php
/**
 * @copyright Copyright (c) 2014 EuroMD LTD
 */

namespace EuroMD\yandexMetrika;

use yii\authclient\clients\YandexOAuth;
use yii\authclient\InvalidResponseException;
use yii\base\InvalidParamException;
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

    /**
     * Pre-processing $params in case if string was given.
     * @param string $apiSubUrl
     * @param string $method
     * @param array|string $params
     * @param array $headers
     * @return array
     */
    public function rawApi($apiSubUrl, $method = 'GET', $params = [], array $headers = [])
    {
        if (is_string($params)) {
            $headers[] = 'Content-Length: ' . mb_strlen($params);
            $headers[] = 'Host: webmaster.yandex.ru';
            $headers[] = $method . ' ' . parse_url($this->apiBaseUrl, PHP_URL_PATH) . $apiSubUrl . ' HTTP/1.1';
            $this->setCurlOptions([CURLOPT_POSTFIELDS => $params]);
            $params = [];
        }
        return $this->api($apiSubUrl, $method, $params, $headers);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return array
     */
    protected function composeRequestCurlOptions($method, $url, array $params)
    {
        $options = parent::composeRequestCurlOptions($method, $url, $params);
        if ($method == 'POST' && !$params) {
            unset($options[CURLOPT_POSTFIELDS], $options[CURLOPT_HTTPHEADER]);
        }
        return $options;
    }

    /**
     * @param \yii\authclient\OAuthToken $accessToken
     * @param string $url
     * @param string $method
     * @param array $params
     * @param array $headers
     * @throws InvalidParamException
     * @return array
     */
    protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        $headers[] = 'Authorization: OAuth ' . $accessToken->token;
        try {
            return $this->sendRequest($method, $url, $params, $headers);
        } catch (InvalidResponseException $e) {
            if ($e->responseHeaders['http_code'] == 201) {
                return $this->processResponse($e->responseBody,
                    $this->determineContentTypeByHeaders($e->responseHeaders));
            }
            throw new InvalidParamException('Request failed with code: ' . $e->responseHeaders['http_code'] . ', message: ' . $e->responseBody);
        }
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