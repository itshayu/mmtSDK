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
            'host' => 'dev.qitui.cdkwtech.com',
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
            'associate' => '391bb884-cd3b-4ca2-b096-45a83cbf3f93',
            'app_uuid' => '6571dba4-ec3a-4272-97f3-630d9bd8e182',
            'path' => '/index/index?sid=17',
            'platform' => 52,
            'share_mode' => 1,
        ]);
        echo $res->getBody();
        // print_r((string)$res->getBody())
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
        $res = $this->client->reportVisits('b8f3176e-161b-405f-a7cd-9bba78f751b8', '6571dba4-ec3a-4272-97f3-630d9bd8e182');
        echo $res->getBody();
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
        $res = $this->client->publishVideo('b8f3176e-161b-405f-a7cd-9bba78f751b8', '6571dba4-ec3a-4272-97f3-630d9bd8e182', '131040415b7f4767505c10155d2c453552512b42007e4c615457424d0d7a436d53514140017f');
        echo $res->getBody();
    }
}