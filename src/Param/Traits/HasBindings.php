<?php

declare(strict_types=1);
/**
 * This file is part of suyar/hyperf-clickhouse.
 *
 * @link     https://github.com/suyar/hyperf-clickhouse
 * @document https://github.com/suyar/hyperf-clickhouse/blob/main/README.md
 * @contact  su@zorzz.com
 * @license  https://github.com/suyar/hyperf-clickhouse/blob/master/LICENSE
 */

namespace Suyar\ClickHouse\Param\Traits;

trait HasBindings
{
    protected array $bindings = [];

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function setBindings(array $bindings): static
    {
        $this->bindings = $bindings;

        return $this;
    }

    public function setBinding(string $key, mixed $value): static
    {
        $this->bindings[$key] = $value;

        return $this;
    }
}
