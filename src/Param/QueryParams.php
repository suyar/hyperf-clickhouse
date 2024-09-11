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

use Psr\Http\Message\StreamInterface;
use Suyar\ClickHouse\Param\Traits\HasBindings;
use Suyar\ClickHouse\Param\Traits\HasDecompressResponse;
use Suyar\ClickHouse\Param\Traits\HasFormat;
use Suyar\ClickHouse\Param\Traits\HasQuery;
use Suyar\ClickHouse\Param\Traits\HasQueryId;
use Suyar\ClickHouse\Param\Traits\HasSessionId;
use Suyar\ClickHouse\Param\Traits\HasSettings;

class QueryParams extends BaseParams
{
    use HasBindings;
    use HasSettings;
    use HasSessionId;
    use HasQueryId;
    use HasFormat;
    use HasQuery;
    use HasDecompressResponse;

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
