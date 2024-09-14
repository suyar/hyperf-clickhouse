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

use Psr\Http\Message\StreamInterface;

trait HasValues
{
    /**
     * @var array|resource|StreamInterface|string
     */
    protected $values = [];

    protected bool $stringAsFile = true;

    /**
     * @return array|resource|StreamInterface|string
     */
    public function getValues()
    {
        return $this->values;
    }

    public function getStringAsFile(): bool
    {
        return $this->stringAsFile;
    }

    /**
     * @param array|resource|StreamInterface|string $values
     * @return $this
     */
    public function setValues($values, bool $stringAsFile = true): static
    {
        $this->values = $values;
        $this->stringAsFile = $stringAsFile;

        return $this;
    }
}
