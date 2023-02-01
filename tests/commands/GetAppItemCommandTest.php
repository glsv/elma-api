<?php

namespace Glsv\tests\commands;

use Glsv\ElmaApi\commands\GetAppItemCommand;
use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\requests\GetAppItemRequest;
use Glsv\ElmaApi\responses\ResultItem;
use PHPUnit\Framework\TestCase;

class GetAppItemCommandTest extends TestCase
{
    public $apiResponse = [
        "success" => true,
        "error" => "",
        "item" => [
            [
                "__id" => "1f12adb6-4730-4d51-ad99-5466085dfcab",
                "__createdAt" => "2023-02-01T07:00:19Z",
                "__createdBy" => "eb8c404a-0fd6-41c3-b153-edacf5ebf20c",
            ],
        ]
    ];

    public function setUp(): void
    {
        $this->api = $this->getMockBuilder(ElmaClientApi::class)->disableOriginalConstructor()->getMock();
    }

    public function testFail()
    {
        $errorMessage = 'error message';
        $this->expectException(ElmaApiRuntimeException::class);
        $this->expectExceptionMessage($errorMessage);

        $apiResponse = $this->apiResponse;
        $apiResponse['success'] = false;
        $apiResponse['error'] = $errorMessage;

        $this->api->method('makePost')->willReturn($apiResponse);

        $c = new GetAppItemCommand($this->api, new GetAppItemRequest('namespace', 'app', 'uuid'));
        $c->execute();
    }

    public function testWrongFormat()
    {
        $this->expectException(ElmaApiRuntimeException::class);
        $this->expectExceptionMessage('"item" attr is missing for response');

        $apiResponse = $this->apiResponse;
        unset($apiResponse['item']);

        $this->api->method('makePost')->willReturn($apiResponse);

        $c = new GetAppItemCommand($this->api, new GetAppItemRequest('namespace', 'app', 'uuid'));
        $c->execute();
    }

    public function testSuccess()
    {
        $this->api->method('makePost')->willReturn($this->apiResponse);

        $c = new GetAppItemCommand($this->api, new GetAppItemRequest('namespace', 'app', 'uuid'));
        $response = $c->execute();

        $this->assertInstanceOf(ResultItem::class, $response);
    }
}
