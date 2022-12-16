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
     * Receive all elements of a list.
     * Pagination of large lists is hidden inside this method
     * @param string $relativeUrl
     * @param array $requestData Request params
     * @param int $pageSize
     * @return array
     * @throws \Glsv\ElmaApi\exceptions\ElmaApiException
     * @throws \JsonException
     */
    public function getAllItems(string $relativeUrl, array $requestData, int $pageSize = 100): array
    {
        $items = [];

        $total = 1;
        $collected = 0;

        $requestData = array_merge(
            [
                "active" => true,
                "size" => $pageSize,
                "sortExpressions" => [
                    [
                        "ascending" => true,
                        "field" => "__index"
                    ]
                ],
            ],
            $requestData,
            [
                "from" => 0
            ]
        );

        while ($collected <= $total) {
            $result = $this->api->makePost($relativeUrl, $requestData);

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