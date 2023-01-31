<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\responses;

class ResultListData
{
    public function __construct(public array $data, public int $total)
    {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCount(): int
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }
}