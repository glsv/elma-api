<?php

namespace Glsv\tests\commands;

use Glsv\ElmaApi\commands\GetUserListCommand;
use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\requests\GetUserListRequest;
use Glsv\ElmaApi\responses\ResultListData;
use PHPUnit\Framework\TestCase;

class GetUserListCommandTest extends TestCase
{
    public $apiResponse = [
        "success" => true,
        "error" => "",
        "result" => [
            "result" => [
                [
                    "__id" => "xxx",
                    "email" => "xxx@ccc.com",
                    "login" => "login",
                ],
                [
                    "__id" => "xxx2",
                    "email" => "xxx2@ccc.com",
                    "login" => "login2",
                ],
            ],
            "total" => 20
        ]
    ];

    public function testFail()
    {
        $errorMessage = 'error message';
        $this->expectException(ElmaApiRuntimeException::class);
        $this->expectExceptionMessage($errorMessage);

        $apiResponse = $this->apiResponse;
        $apiResponse['success'] = false;
        $apiResponse['error'] = $errorMessage;

        $api = $this->getMockBuilder(ElmaClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('makePost')->willReturn($apiResponse);

        $c = new GetUserListCommand($api, new GetUserListRequest());
        $c->execute();
    }

    public function testFailWrongFormat()
    {
        $this->expectException(ElmaApiRuntimeException::class);

        $apiResponse = $this->apiResponse;
        unset($apiResponse['result']['result']);

        $api = $this->getMockBuilder(ElmaClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('makePost')->willReturn($apiResponse);

        $c = new GetUserListCommand($api, new GetUserListRequest());
        $c->execute();
    }

    public function testSuccess()
    {
        $api = $this->getMockBuilder(ElmaClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('makePost')->willReturn($this->apiResponse);

        $c = new GetUserListCommand($api, new GetUserListRequest());
        $result = $c->execute();

        $this->assertInstanceOf(ResultListData::class, $result);
        $this->assertSame($this->apiResponse['result']['total'], $result->getTotal());
        $this->assertSame(count($this->apiResponse['result']['result']), $result->getCount());
    }
}