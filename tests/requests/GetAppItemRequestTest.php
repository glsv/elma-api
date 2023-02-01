<?php

namespace Glsv\tests\requests;

use Glsv\ElmaApi\exceptions\ElmaApiInvalidParamsException;
use Glsv\ElmaApi\requests\GetAppItemRequest;
use PHPUnit\Framework\TestCase;

class GetAppItemRequestTest extends TestCase
{
    public function testEmptyNamespace()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);
        new GetAppItemRequest('', 'x', 'x');
    }

    public function testEmptyApp()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);
        new GetAppItemRequest('x', '', 'x');
    }

    public function testEmptyId()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);
        new GetAppItemRequest('x', 'x', '');
    }

    public function testSuccess()
    {
        $r = new GetAppItemRequest('namespace', 'x', 'x');
        $this->assertSame('namespace', $r->namespace);
    }
}
