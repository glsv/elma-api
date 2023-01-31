<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\commands;

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\exceptions\ElmaApiRuntimeException;
use Glsv\ElmaApi\interfaces\CommandInterface;
use Glsv\ElmaApi\requests\GetUserListRequest;
use Glsv\ElmaApi\responses\ResultListData;

class GetUserListCommand implements CommandInterface
{
    protected $url = '/pub/v1/user/list';
    protected ElmaClientApi $api;
    protected $request;

    public function __construct(ElmaClientApi $api, GetUserListRequest $request)
    {
        $this->api = $api;
        $this->request = $request;
    }


    public function execute(): ResultListData
    {
        $result = $this->api->makePost($this->url, $this->request->buildBody());

        if (!isset($result['success']) || $result['success'] !== true) {
            throw new ElmaApiRuntimeException($result['error'] ?? 'Unknown error');
        }

        if (!isset($result['result']['result']) || !isset($result['result']['total'])) {
            throw new ElmaApiRuntimeException('Format of response is wrong: ' . json_encode($result));
        }

        return new ResultListData($result['result']['result'], $result['result']['total']);
    }
}