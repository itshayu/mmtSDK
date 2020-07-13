<?php
namespace Itshayu\MmtSDK;

use Psr\Http\Message\ResponseInterface;

class Api
{
    /**
    * 静态成品变量 保存全局实例
    */
    private static $_instance = NULL;

    private $client;

    /**
     * 静态工厂方法，返还此类的唯一实例
     *
     * @param array $config
     * @return Api
     */
    public static function getInstance(array $config) : Api
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    public function __construct(array $config)
    {
        $this->client = Client::getInstance($config);
    }

    /**
     * 返回结果处理
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function result(ResponseInterface $response) : array
    {
        if ($response->getStatusCode() !== 200) {
            throw new \Exception($response->getBody());
        }

        $res = json_decode($response->getBody(), true);
        if ($res['code'] === 200) {
            return $res['data'];
        }

        throw new \Exception($res);
    }

    /**
     * 发送订单
     *
     * @return void
     */
    public function report(array $data) : ResponseInterface
    {
        $res = $this->client->post('/service/v1/report', $data);
        return $res;
    }

    /**
     * 获得当前用户已授权的抖音号
     *
     * @param string $promoter
     * @return ResponseInterface
     */
    public function getAuthUser(string $promoter) : array
    {
        try {
            $res = $this->client->get('/service/v1/getAuthUser/' . $promoter);
            return $this->result($res);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 报告访问数量
     *
     * @param string $promoter
     * @param string $appUuid
     * @return ResponseInterface
     */
    public function reportVisits(string $promoter, string $appUuid) : ResponseInterface
    {
        $res = $this->client->get('/service/v1/report/visits/' . $promoter . '/' . $appUuid);
        return $res;
    }

    /**
     * 发布视频上报视频ID
     *
     * @param string $promoter 推广者
     * @param string $appUuid APP
     * @param string $videoId 发布的视频ID
     * @return ResponseInterface
     */
    public function publishVideo(string $promoter, string $appUuid, string $videoId) : ResponseInterface
    {
        $res = $this->client->get('/service/v1/publish/video/' . $promoter . '/' . $appUuid . '/' . $videoId);
        return $res;
    }
}