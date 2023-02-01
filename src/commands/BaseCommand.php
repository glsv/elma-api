<?php

namespace Glsv\ElmaApi\commands;

use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\interfaces\CommandInterface;

abstract class BaseCommand implements CommandInterface
{
    protected $apiPrefix = '/pub/v1';

    protected function validateCommonResponse(array $response)
    {
        if (!isset($response['success']) || $response['success'] !== true) {
            throw new ElmaApiRuntimeException($response['error'] ?? 'Unknown error');
        }
    }
}