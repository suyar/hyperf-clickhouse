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

trait HasPersistTo
{
    /**
     * @var resource|StreamInterface|string
     */
    protected $persistTo = '';

    /**
     * @param resource|StreamInterface|string $persistTo
     * @return $this
     */
    public function setPersistTo($persistTo): static
    {
        $this->persistTo = $persistTo;

        return $this;
    }

    /**
     * @return resource|StreamInterface|string
     */
    public function getPersistTo()
    {
        return $this->persistTo;
    }
}
