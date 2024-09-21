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
use Suyar\ClickHouse\Param\Traits\HasDatabase;
use Suyar\ClickHouse\Param\Traits\HasDecompressResponse;
use Suyar\ClickHouse\Param\Traits\HasFormat;
use Suyar\ClickHouse\Param\Traits\HasPersistTo;
use Suyar\ClickHouse\Param\Traits\HasProgress;
use Suyar\ClickHouse\Param\Traits\HasQuery;
use Suyar\ClickHouse\Param\Traits\HasQueryId;
use Suyar\ClickHouse\Param\Traits\HasSessionId;
use Suyar\ClickHouse\Param\Traits\HasSettings;

class QueryParams extends BaseParams
{
    use HasDatabase;
    use HasProgress;
    use HasBindings;
    use HasSettings;
    use HasSessionId;
    use HasQueryId;
    use HasFormat;
    use HasQuery;
    use HasDecompressResponse;
    use HasPersistTo;
}
