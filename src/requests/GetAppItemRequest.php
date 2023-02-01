<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\requests;

use Glsv\ElmaApi\exceptions\ElmaApiInvalidParamsException;

class GetAppItemRequest
{
    public function __construct(public string $namespace, public string $appId, public string $id)
    {
        if ($namespace === "") {
            throw new ElmaApiInvalidParamsException('namespace is empty');
        }

        if ($appId === "") {
            throw new ElmaApiInvalidParamsException('appId is empty');
        }

        if ($id === "") {
            throw new ElmaApiInvalidParamsException('id is empty');
        }
    }
}