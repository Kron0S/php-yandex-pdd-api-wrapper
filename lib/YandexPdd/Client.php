<?php

namespace YandexPdd;

use Buzz\Client\Curl;
use Buzz\Client\ClientInterface;

use YandexPdd\Api\ApiInterface;
use YandexPdd\Exception\InvalidArgumentException;
use YandexPdd\HttpClient\HttpClient;
use YandexPdd\HttpClient\HttpClientInterface;
use YandexPdd\HttpClient\Listener\AuthListener;

/**
 * Simple API wrapper for yandex-pdd
 */
class Client
{
    /**
     * @var array
     */
    private $options = array(
        'user_agent'  => 'php-yandex-pdd-api',
        'timeout'     => 60
    );

    private $base_url = null;

    /**
     * The Buzz instance used to communicate with yandex-pdd
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new yandex-pdd client
     *
     * @param string               $baseUrl
     * @param null|ClientInterface $httpClient Buzz client
     */
    public function __construct($baseUrl, ClientInterface $httpClient = null)
    {
        $httpClient = $httpClient ?: new Curl();
        $httpClient->setTimeout($this->options['timeout']);
        $httpClient->setVerifyPeer(false);

        $this->base_url     = $baseUrl;
        $this->httpClient   = new HttpClient($this->base_url, $this->options, $httpClient);
    }

    /**
     * @param string $name
     *
     * @return ApiInterface
     *
     * @throws InvalidArgumentException
     */
    public function api($name)
    {
        switch ($name) {
            case 'ext':
                $api = new Api\Ext($this);
                break;

            case 'cdr':
                $api = new Api\Cdr($this);
                break;

            case 'user':
                $api = new Api\User($this);
                break;

            case 'outuser':
                $api = new Api\OutUser($this);
                break;

            case 'template':
                $api = new Api\Template($this);
                break;

            default:
                throw new InvalidArgumentException();
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $token      yandex-pdd private token
     * @param null|string $authMethod One of the AUTH_* class constants
     * @param null|string $sudo
     */
    public function authenticate($token, $url, $authMethod = null, $sudo = null)
    {
        $this->httpClient->addListener(
            new AuthListener(
                $authMethod,
                $url,
                $token,
                $sudo
            )
        );
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setBaseUrl($url)
    {
        $this->base_url = $url;
    }
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->httpClient->clearHeaders();
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->httpClient->setHeaders($headers);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        $this->options[$name] = $value;
    }
}
