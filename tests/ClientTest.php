<?php
namespace Tests;

use Itshayu\MmtSDK\Api;
use PHPUnit\Framework\TestCase;

use function GuzzleHttp\json_decode;

class ClientTest extends TestCase
{
    private $client;

    protected function setUp() : void
    {
        $this->client = Api::getInstance([
            'appid' => '6MqHy1xzB21Nxulq',
            'appsecret' => 'VgzCa3aBpdd3z8LVTBJZvjJTulwcASlc',
            'scheme' => 'http',
            'host' => 'dev.mmt.cdkwtech.com',
            'identity' => 'TLP'
        ]);
    }

    /**
     * 增加报告
     * ./vendor/bin/phpunit ./tests/ClientTest.php --filter testReport
     *
     * @return void
     */
    public function testReport()
    {
        $res = $this->client->report([
            'associate' => '9e4238a4-0e80-4095-8de5-cb86c03a6bac',
            'app_uuid' => '6571dba4-ec3a-4272-97f3-630d9bd8e182',
            'path' => '/index/index?sid=17',
            'platform' => 52,
            'share_mode' => 1,
            'video_uuid' => '1668d71c-686f-4ccb-a095-1af88f991241'
        ]);
        var_dump($res);
        exit();
    }

    /**
     * 报告访问数量
     * ./vendor/bin/phpunit ./tests/ClientTest.php --filter testReportVisits
     *
     * @param Type $var
     * @return void
     */
    public function testReportVisits()
    {
        /**
         * 增加当日访问量
         *
         * @param string $promoter 推广者uuid
         * @param string $appUuid 应用uuid
         * @param string $vedioUuid 视频uuid
         * @return void
         */
        $res = $this->client->reportVisits(
            'b8f3176e-161b-405f-a7cd-9bba78f751b8',
            '6571dba4-ec3a-4272-97f3-630d9bd8e182',
            ''
            // '1668d71c-686f-4ccb-a095-1af88f991241'
        );
        var_dump($res);
        exit();
        // print_r(json_decode((string)$res->getBody()));
    }

    /**
     * 获得用户已授权的抖音号
     * ./vendor/bin/phpunit ./tests/ClientTest.php --filter testGetAuthUser
     *
     * @return void
     */
    public function testGetAuthUser()
    {
        // $client = Api::getInstance([
        //     'appid' => '6MqHy1xzB21Nxulq',
        //     'appsecret' => 'VgzCa3aBpdd3z8LVTBJZvjJTulwcASlc',
        //     'scheme' => 'http',
        //     'host' => 'dev.qitui.cdkwtech.com',
        //     'identity' => 'TLP'
        // ]);

        $res = $this->client->getAuthUser('aa25249a-8259-4d26-94d5-bad9d454d496');
        print_r($res);
    }

    /**
     * 发布视频完成
     * ./vendor/bin/phpunit ./tests/ClientTest.php --filter testPublishVideo
     *
     * @return void
     */
    public function testPublishVideo()
    {
        $res = $this->client->publishVideo(
            'b8f3176e-161b-405f-a7cd-9bba78f751b8',
            '6571dba4-ec3a-4272-97f3-630d9bd8e182',
            '/pages/info/info?sid=58',
            '131040415b7f4767505c10155d2c453552512b42007e4c615457424d0d7a436d53514140017f',
            '28f30088-d6f8-4cf1-81a5-da62b426e645'
        );
        print_r($res);
    }
}