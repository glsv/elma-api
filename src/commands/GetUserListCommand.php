<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\commands;

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\requests\GetUserListRequest;
use Glsv\ElmaApi\responses\ResultListData;

class GetUserListCommand extends BaseCommand
{
    protected $url = 'user/list';
    protected ElmaClientApi $api;
    protected $request;

    public function __construct(ElmaClientApi $api, GetUserListRequest $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    public function execute(): ResultListData
    {
        $response = $this->api->makePost($this->apiPrefix . '/' . $this->url, $this->request->buildBody());

        $this->validateCommonResponse($response);

        if (!isset($response['result']['result']) || !isset($response['result']['total'])) {
            throw new ElmaApiRuntimeException('Format of response is wrong: ' . json_encode($response));
        }

        return new ResultListData($response['result']['result'], $response['result']['total']);
    }
}