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

use Psr\Http\Message\ResponseInterface;
use Suyar\ClickHouse\Param\BaseParams;

/**
 * @property string $format
 * @property string $queryId
 * @property string $timezone
 * @property ?Summary $summary
 */
abstract class BaseResult
{
    protected string $format;

    protected string $queryId;

    protected string $timezone;

    protected ?Summary $summary;

    public function __construct(
        protected ResponseInterface $response,
        protected BaseParams $params
    ) {
        $this->format = $this->response->getHeaderLine('X-Clickhouse-Format');
        $this->queryId = $this->response->getHeaderLine('X-Clickhouse-Query-Id');
        $this->timezone = $this->response->getHeaderLine('X-Clickhouse-Timezone');

        if ($summary = $this->response->getHeaderLine('X-Clickhouse-Summary')) {
            $this->summary = new Summary(json_decode($summary, true));
        }
    }

    public function __get(string $name)
    {
        return property_exists($this, $name) ? $this->{$name} : null;
    }
}
