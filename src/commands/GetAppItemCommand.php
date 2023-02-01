<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\commands;

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\requests\GetAppItemRequest;
use Glsv\ElmaApi\responses\ResultItem;

class GetAppItemCommand extends BaseCommand
{
    public function __construct(private ElmaClientApi $api, private GetAppItemRequest $request)
    {
    }

    public function execute()
    {
        $relativeUrl = $this->apiPrefix . '/app/' .
            $this->request->namespace . '/' .
            $this->request->appId . '/' .
            $this->request->id . '/get';

        $response = $this->api->makePost($relativeUrl, []);

        $this->validateCommonResponse($response);

        if (!isset($response['item'])) {
            throw new ElmaApiRuntimeException('"item" attr is missing for response: ' . json_encode($response));
        }

        return new ResultItem($response['item']);
    }
}