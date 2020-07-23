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
    public function result(ResponseInterface $response) : ?array
    {
        // if ($response->getStatusCode() !== 200) {
        //     throw new \Exception($response->getBody());
        // }
        try {
            return json_decode($response->getBody(), true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 发送订单
     *
     * @return void
     */
    public function report(array $data) : ?array
    {
        try {
            $res = $this->client->post('/service/v1/report', $data);
            return $this->result($res);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 获得当前用户已授权的抖音号
     *
     * @param string $promoter
     * @return ResponseInterface
     */
    public function getAuthUser(string $promoter) : ?array
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
    public function reportVisits(string $promoter, string $appUuid, string $videoUuid) : ?array
    {
        try {
            $res = $this->client->get(
                sprintf('/service/v1/report/visits/%s/%s/%s', $promoter, $appUuid, $videoUuid)
            );
            return $this->result($res);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 发布视频上报视频ID
     *
     * @param string $videoId 拍摄后的视频ID
     * @param string $videoUuid 拍摄视频UUID
     * @param string $userUuid 平台用户UUID
     * @param string $appUuid 平台APPID
     * @param string $appPath 拍摄时应用路径
     * @param string $authDy 平台授权的抖音号
     * @return array
     */
    public function publishVideo(
        string $videoId,
        string $videoUuid,
        string $userUuid,
        string $appUuid,
        string $appPath,
        string $authDy
    ) : ?array
    {
        try {
            $res = $this->client->post('/service/v1/publish/video', [
                'videoId' => $videoId,
                'videoUuid' => $videoUuid,
                'userUuid' => $userUuid,
                'appUuid' => $appUuid,
                'appPath' => $appPath,
                'authDyUuid' => $authDy,
            ]);
            return $this->result($res);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}