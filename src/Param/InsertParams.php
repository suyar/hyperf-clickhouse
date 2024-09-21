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

namespace Suyar\ClickHouse\Param;

use Suyar\ClickHouse\Param\Traits\HasBindings;
use Suyar\ClickHouse\Param\Traits\HasCompressRequest;
use Suyar\ClickHouse\Param\Traits\HasDatabase;
use Suyar\ClickHouse\Param\Traits\HasFormat;
use Suyar\ClickHouse\Param\Traits\HasProgress;
use Suyar\ClickHouse\Param\Traits\HasQueryId;
use Suyar\ClickHouse\Param\Traits\HasSessionId;
use Suyar\ClickHouse\Param\Traits\HasSettings;
use Suyar\ClickHouse\Param\Traits\HasValues;

class InsertParams extends BaseParams
{
    use HasDatabase;
    use HasProgress;
    use HasBindings;
    use HasSettings;
    use HasSessionId;
    use HasQueryId;
    use HasFormat;
    use HasValues;
    use HasCompressRequest;

    protected string $table = '';

    protected array $columns = [];

    private bool $except = false;

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function getExcept(): bool
    {
        return $this->except;
    }

    public function setExcept(bool $except = true): static
    {
        $this->except = $except;

        return $this;
    }
}
