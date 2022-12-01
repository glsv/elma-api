<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\services;

use Glsv\ElmaApi\ElmaClientApi;

class ListService
{
    public function __construct(protected ElmaClientApi $api)
    {
    }

    /**
     * Receive all active elements of list
     * @param string $listKey
     * @param int $pageSize
     * @return array
     * @throws \Glsv\ElmaApi\exceptions\ElmaApiException
     * @throws \JsonException
     */
    public function getAllItems(string $listKey, int $pageSize = 50): array
    {
        $items = [];

        $total = 1;
        $collected = 0;

        $requestData = [
            "active" => true,
            "size" => $pageSize,
            "sortExpressions" => [
                [
                    "ascending" => true,
                    "field" => "__index"
                ]
            ],
            "from" => 0
        ];

        while ($collected <= $total) {
            $result = $this->api->makePost($listKey . '/list', $requestData);

            $total = $result['result']['total'] ?? 0;
            $list = $result['result']['result'] ?? [];

            if (!$list) {
                return $items;
            }

            foreach ($list as $item) {
                $items[] = $item;
                $collected++;
            }

            $requestData['from'] = $collected;
        }

        return $items;
    }
}