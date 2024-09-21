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

trait HasDatabase
{
    protected string $databases = '';

    public function getDatabase(): string
    {
        return $this->databases;
    }

    public function setDatabase(string $database): static
    {
        $this->databases = $database;

        return $this;
    }
}
