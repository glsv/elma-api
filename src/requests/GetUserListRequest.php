<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\requests;

use Glsv\ElmaApi\requests\params\TfFilters;
use Glsv\ElmaApi\requests\params\SortExpression;
use Glsv\ElmaApi\exceptions\ElmaApiInvalidParamsException;
use Glsv\ElmaApi\interfaces\RequestInterface;

class GetUserListRequest implements RequestInterface
{
    const SIZE_MAX = 100;

    private int $from;
    private array $ids = [];
    private $size = 100;

    /**
     * @var SortExpression[]
     */
    private array $sorts = [];

    private ?TfFilters $filters = null;

    public function __construct(int $from = 0, int $size = 100, array $sorts = [])
    {
        $this->from = $from;
        $this->setSize($size);
        $this->setSorts($sorts);
    }

    /**
     * @param SortExpression[] $sorts
     * @return void
     */
    public function setSorts(array $sorts): void
    {
        array_walk($sorts, function (SortExpression $sort){});
        $this->sorts = $sorts;
    }

    public function setIds(array $ids): void
    {
        $ids = array_filter($ids);
        array_walk($ids, function ($id) {
            if (!is_string($id)) {
                throw new ElmaApiInvalidParamsException(
                    sprintf('id must have string type. received type %s, value = %s', gettype($id), $id)
                );
            }
        });

        $this->ids = $ids;
    }

    public function setSize(int $size): void
    {
        if ($size > self::SIZE_MAX) {
            throw new ElmaApiInvalidParamsException('size can`t be more than ' . self::SIZE_MAX);
        }

        $this->size = $size;
    }

    public function setFilters(TfFilters $filters): void
    {
        $this->filters = $filters;
    }

    public function buildBody(): array
    {
        $params = [
            'from' => $this->from,
            'size' => $this->size,
        ];

        if ($this->ids) {
            $params['ids'] = $this->ids;
        }

        if ($this->filters) {
            $params['filter'] = $this->filters->toJson();
        }

        if ($this->sorts) {
            $params['sortExpressions'] = array_reduce(
                $this->sorts,
                function ($result, SortExpression $item) {
                    $result[] = $item->toJson();
                    return $result;
                }
            );
        }

        return $params;
    }
}