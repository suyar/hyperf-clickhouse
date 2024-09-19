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

namespace Suyar\ClickHouse\Result;

/**
 * @property string $read_rows
 * @property string $read_bytes
 * @property string $written_rows
 * @property string $written_bytes
 * @property string $total_rows_to_read
 * @property string $result_rows
 * @property string $result_bytes
 * @property string $elapsed_ns
 * @property ?string $real_time_microseconds
 */
class Summary
{
    public function __construct(protected array $summary)
    {
    }

    public function __get(string $name)
    {
        return array_key_exists($name, $this->summary) ? $this->summary[$name] : null;
    }
}
