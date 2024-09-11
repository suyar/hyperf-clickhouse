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

trait HasCompressRequest
{
    protected ?bool $compressRequest = null;

    public function setCompressRequest(?bool $compressRequest): static
    {
        $this->compressRequest = $compressRequest;

        return $this;
    }

    public function getCompressRequest(): ?bool
    {
        return $this->compressRequest;
    }
}
