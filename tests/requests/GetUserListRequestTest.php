<?php

declare(strict_types=1);

namespace Glsv\tests\requests;

use Glsv\ElmaApi\requests\params\SortExpression;
use Glsv\ElmaApi\requests\params\TfFilters;
use Glsv\ElmaApi\exceptions\ElmaApiInvalidParamsException;
use Glsv\ElmaApi\requests\GetUserListRequest;
use PHPUnit\Framework\TestCase;

class GetUserListRequestTest extends TestCase
{
    public function testFailWrongUserIds()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);

        $r = new GetUserListRequest();
        $r->setIds([1, 2]);
    }

    public function testFailExceededSize()
    {
        $this->expectException(ElmaApiInvalidParamsException::class);

        new GetUserListRequest(0, 1000);
    }

    public function testSuccessBuild()
    {
        $expected = [
            "from" => 1,
            "size" => 50,
            "ids" => [
                'uuid_1',
                'uuid_2'
            ],
            "filter" => [
                "tf" => [
                    "email" => "xxx@yyy.com"
                ]
            ],
            "sortExpressions" => [
                [
                    "field" => "login",
                    "ascending" => false
                ]
            ]
        ];

        $r = new GetUserListRequest($expected["from"], $expected["size"]);
        $r->setIds(['uuid_1', 'uuid_2']);

        $f = new TfFilters();
        $f->addFilter('email', 'xxx@yyy.com');
        $r->setFilters($f);

        $r->setSorts([new SortExpression('login', false)]);

        $this->assertSame($expected, $r->buildBody());
    }
}
