<?php
namespace Itshayu\MmtSDK;

use Psr\Http\Message\ResponseInterface;

class Client
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const HTTP_DEFAULT_PORT = 80;
    const HTTPS_DEFAULT_PORT = 443;

    const FORM_DATA_METHODS = [
        'POST',
        'PUT',
        'DELETE'
    ];

    const SUPPORTED_HTTP_METHODS = [
        'get',
        'post',
        'put',
        'delete',
    ];

    protected $params = [];
    protected $appId;
    protected $appSecret;
    protected $identity;
    protected $host;
    protected $ip;
    protected $scheme;
    protected $port;
    protected $method;
    protected $path;
    protected $loggerHandler;
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;
    protected $certPem;

    /**
    * 静态成品变量 保存全局实例
    */
    private static $_instance = NULL;

    /**
     * 静态工厂方法，返还此类的唯一实例
    */
    public static function getInstance(array $config) : Client
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    public function __construct(array $config)
    {
        $this->httpClient = new \GuzzleHttp\Client;

        $this->setAppId($config['appid'])
            ->setAppSecret($config['appsecret'])
            ->setScheme($config['scheme'])
            ->setHost($config['host'])
            ->setIdentity($config['identity']);
    }

    public function setAppId(string $appId) : Client
    {
        $this->appId = $appId;
        return $this;
    }

    protected function getAppId() : string
    {
        return $this->appId;
    }

    public function setAppSecret(string $appSecret) : Client
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    protected function getAppSecret() : string
    {
        return $this->appSecret;
    }

    protected function clearDatas()
    {
        $this->params = [];
    }

    protected function setArrayDatas(array $params) : Client
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
        return $this;
    }

    protected function setMethod(string $method)
    {
        $this->method = \strtoupper($method);

        return $this;
    }

    protected function getMethod() : string
    {
        return $this->method;
    }

    protected function setPath(string $path) : Client
    {
        $path = \rtrim($path, '/');
        if (\strpos($path, '/') !== 0) {
            $path = '/'.$path;
        }
        $this->path = $path;
        return $this;
    }

    protected function getPath() : string
    {
        return $this->path;
    }

    public function setScheme(string $scheme) : Client
    {
        if ($scheme) {
            $scheme = \strtolower($scheme);

            if (! in_array($scheme, [self::SCHEME_HTTP, self::SCHEME_HTTPS])) {
                throw new \InvalidArgumentException('The supported schemes are : http and https');
            }
            $this->scheme = $scheme;
        }

        return $this;
    }

    protected function getScheme() : string
    {
        return $this->scheme ?: 'http';
    }

    public function setIp(string $ip) : Client
    {
        if ($ip) {
            $ip = \ltrim($ip, 'http://');
            $ip = \ltrim($ip, 'https://');
            $ip = \rtrim($ip, '/');

            $this->ip = $ip;
        }

        return $this;
    }

    protected function getIp() : ?string
    {
        return $this->ip;
    }

    public function setHost($host) : Client
    {
        $host = \trim($host);
        $host = \str_replace('http://', '', $host);
        $host = \str_replace('https://', '', $host);
        $host = \rtrim($host, '/');

        $this->host = $host;
        return $this;
    }

    protected function getHost() : string
    {
        return $this->host;
    }

    public function setPort(int $port) : Client
    {
        if ($port) {
            $this->port = \intval($port);
        }

        return $this;
    }

    protected function getPort() : int
    {
        if ($this->port) {
            return $this->port;
        }

        if ($this->scheme == self::SCHEME_HTTPS) {
            return self::HTTPS_DEFAULT_PORT;
        }

        return self::HTTP_DEFAULT_PORT;
    }

    public function setIdentity(string $identity) : Client
    {
        $this->identity = $identity;
        return $this;
    }

    protected function getIdentity() : ?string
    {
        return $this->identity;
    }

    protected function setDatas(string $key, $value = null) : Client
    {
        if (is_array($key)) {
            return $this->setArrayDatas($key);
        }
        $this->params[$key] = $value;

        return $this;
    }

    protected function getDatas() : array
    {
        return $this->params;
    }

    public function setCertPem($certPem) : Client
    {
        $this->certPem = $certPem;

        return $this;
    }

    protected function getCertPem()
    {
        return $this->certPem;
    }

    protected function createUrl(string $path) : string
    {
        $this->setPath($path);
        $scheme = $this->getScheme();
        $url = $scheme.'://';

        if ($ip = $this->getIp()) {
            $url .= $ip;
        } else {
            $url .= $this->getHost();
        }

        $port = $this->getPort();
        if (($scheme == 'http' && $port != self::HTTP_DEFAULT_PORT) || ($scheme == 'https' && $port != self::HTTPS_DEFAULT_PORT)) {
            $url .= ':'.$port;
        }
        return $url.$this->getPath().'?'. $this->createSignData();
    }

    protected function createNonce()
    {
        $identity = $this->getIdentity();

        $hash = \md5(\uniqid($identity, true).'-'.\random_int(1, 65535).'-'.\random_int(1, 65535));

        return $identity.':'.substr($hash, 0, 8).
                            '-'.
                            \substr($hash, 8, 4).
                            '-'.
                            \substr($hash, 12, 4).
                            '-'.
                            \substr($hash, 16, 4).
                            '-'.
                            \substr($hash, 20, 12);
    }

    protected function createSignData() : string
    {
        $signData = [];
        $signData['appid'] = $this->getAppId();
        $signData['timestamp'] = time();
        $nonce = $this->createNonce();
        $signData['nonce'] = $nonce;
        $signature = Signature::getInstance();

        $signData['sign'] = $signature->sign(\array_merge($signData, [
            'http_method' => $this->getMethod(),
            'http_path'   => $this->getPath(),
        ]), $this->getAppSecret());

        if ($this->getMethod() == 'GET') {
            if (\count(array_diff($signData, $this->getDatas())) != count($signData)) {
                throw new \InvalidArgumentException('Arguments conflicts');
            }
            $signData = array_merge($signData, $this->getDatas());
        }

        $signData = http_build_query($signData, null, '&');
        return $signData;
    }

    protected function createHttpData(array $headers) : array
    {
        $data = [];
        $data['http_errors'] = false;
        $data['headers'] = [];

        if(\in_array($this->getMethod(), self::FORM_DATA_METHODS)) {
            $data['json'] = $this->getDatas();
        }

        foreach ($headers as $header => $value) {
            $data['headers'][$header] = $value;
        }

        if ($this->getIp()) {
            $data['headers'] = [
                'Host' => $this->getHost(),
            ];
        }

        if ($this->getScheme() == self::SCHEME_HTTPS) {
            $data['verify'] = $this->getCertPem();
        }

        if(empty($data['headers'])) {
            unset($data['headers']);
        }

        return $data;
    }

    public function request(string $path, array $headers) : ResponseInterface
    {
        $url = $this->createUrl($path);
        $data = $this->createHttpData(array_merge($headers, [
            'Accept' => 'application/json'
        ]));

        $response = $this->httpClient->request($this->getMethod(), $url, $data);
        return $response;
    }

    public function __call(string $method, array $args) : ResponseInterface
    {
        if (count($args) < 1) {
            throw new \InvalidArgumentException('Magic request methods require at least a URI');
        }

        $path    = $args[0];
        $datas   = $args[1] ?? [];
        $headers = $args[2] ?? [];

        if ( ! \in_array($method, self::SUPPORTED_HTTP_METHODS)) {
            throw new \InvalidArgumentException('The magic method is not supported');
        }

        $this->clearDatas();
        $this->setArrayDatas($datas);
        $this->setMethod($method);
        return $this->request($path, $headers);
    }
}
