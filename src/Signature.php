<?php
namespace Itshayu\MmtSDK;

class Signature
{
    protected $signKeys = [
        'appid',
        'timestamp',
        'nonce',
        'http_method',
        'http_path',
    ];

    /**
    * 静态成品变量 保存全局实例
    */
    private static $_instance = NULL;

    /**
     * 静态工厂方法，返还此类的唯一实例
    */
    public static function getInstance() : self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function sign(array $params, string $secret) : string
    {
        $params = array_filter($params, function ($value, $key) {
            return in_array($key, $this->signKeys);
        }, ARRAY_FILTER_USE_BOTH);

        ksort($params);

        return hash_hmac('sha256', http_build_query($params, null, '&'), $secret);
    }
}