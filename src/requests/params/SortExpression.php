<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\requests\params;

use Glsv\ElmaApi\exceptions\ElmaApiInvalidParamsException;
use Glsv\ElmaApi\interfaces\RequestParamsInterface;

class SortExpression implements RequestParamsInterface
{
    public function __construct(private string $field, private bool $isAscending = true)
    {
        if ($this->field === "") {
            throw new ElmaApiInvalidParamsException('field can`t be empty');
        }
    }

    public function toJson(): array
    {
        return [
            'field' => $this->field,
            'ascending' => $this->isAscending,
        ];
    }
}