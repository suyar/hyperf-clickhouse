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

trait HasDecompressResponse
{
    protected ?bool $decompressResponse = null;

    public function setDecompressResponse(?bool $decompressResponse): static
    {
        $this->decompressResponse = $decompressResponse;

        return $this;
    }

    public function getDecompressResponse(): ?bool
    {
        return $this->decompressResponse;
    }
}
