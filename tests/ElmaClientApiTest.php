<?php

namespace Glsv\tests;

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\{ElmaApiInvalidParamsException, ElmaApiRuntimeException};
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ElmaClientApiTest extends TestCase
{
    /**
     * @var Client
     */
    private $mockClient;

    public function setUp(): void
    {
        $this->mockClient = $this->getMockBuilder(Client::class)->getMock();
    }

    public function testEmptyBaseUrl()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);
        new ElmaClientApi("", "token");
    }

    public function testEmptyToken()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);
        new ElmaClientApi("http://xyz.com", "");
    }

    public function testWrongResponse()
    {
        $this->expectException(ElmaApiRuntimeException::class);

        $response = new Response(200, [], 'Wrong format');
        $this->mockClient->method('post')->willReturn($response);
        $app = new ElmaClientApi("http://xyz.com", "xxx", $this->mockClient);

        $app->makePost('xxx', []);
    }

    /**
     * @dataProvider errorHttpCodes
     */
    public function testErrorCodesResponse($httpCode)
    {
        $this->expectException(ElmaApiRuntimeException::class);

        $templateResponse = [
            'success' => true,
            'error' => "",
            'result' => [],
        ];

        $response = new Response($httpCode, [], json_encode($templateResponse));
        $this->mockClient->method('post')->willReturn($response);
        $app = new ElmaClientApi("http://xyz.com", "xxx", $this->mockClient);

        $app->makePost('xxx', []);
    }

    public function testNoSuccessInResponse()
    {
        $this->expectException(ElmaApiRuntimeException::class);

        $responseContent = [
            'error' => '',
            'result' => [],
        ];

        $response = new Response(200, [], json_encode($responseContent));
        $this->mockClient->method('post')->willReturn($response);
        $app = new ElmaClientApi("http://xyz.com", "xxx", $this->mockClient);

        $app->makePost('xxx', []);
    }

    public function testSuccessIsFalseInResponse()
    {
        $this->expectException(ElmaApiRuntimeException::class);

        $responseContent = [
            'success' => false,
            'error' => '',
            'result' => [],
        ];

        $response = new Response(200, [], json_encode($responseContent));
        $this->mockClient->method('post')->willReturn($response);
        $app = new ElmaClientApi("http://xyz.com", "xxx", $this->mockClient);

        $app->makePost('xxx', []);
    }

    public function testSuccess()
    {
        $responseContent = [
            'success' => true,
            'error' => '',
            'result' => [],
        ];

        $response = new Response(200, [], json_encode($responseContent));
        $this->mockClient->method('post')->willReturn($response);
        $app = new ElmaClientApi("http://xyz.com", "xxx", $this->mockClient);

        $data = $app->makePost('xxx', []);

        $this->assertSame($responseContent, $data);
    }

    public function errorHttpCodes(): array
    {
        return [
            [400],
            [401],
            [403],
            [404],
            [500],
        ];
    }
}
