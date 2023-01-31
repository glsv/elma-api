<?php

declare(strict_types=1);

namespace Glsv\ElmaApi\requests\params;

use Glsv\ElmaApi\interfaces\RequestParamsInterface;

class TfFilters implements RequestParamsInterface
{
    private array $filters = [];

    public function addFilter(string $attr, $value): void
    {
        $this->filters[$attr] = $value;
    }

    public function toJson(): array
    {
        if (empty($this->filters)) {
            return [];
        }

        return ['tf' => $this->filters];
    }
}