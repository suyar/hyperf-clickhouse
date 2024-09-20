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

namespace Suyar\ClickHouse\Transport;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @property string $format
 * @property string $queryId
 * @property string $timezone
 * @property ?Summary $summary
 * @property ?StreamInterface $stream Returns only if the content is not empty
 * @property ?array $data
 * @property ?array $meta
 * @property ?int $rows
 * @property ?int $rowsBeforeLimitAtLeast
 * @property ?array $statistics
 * @property ?array $totals
 * @property ?array $extremes
 */
class Response
{
    protected string $format = '';

    protected string $queryId = '';

    protected string $timezone = '';

    protected ?Summary $summary = null;

    protected ?StreamInterface $stream = null;

    protected ?array $data = null;

    protected ?array $meta = null;

    protected ?int $rows = null;

    protected ?int $rowsBeforeLimitAtLeast = null;

    protected ?array $statistics = null;

    protected ?array $totals = null;

    protected ?array $extremes = null;

    public function __construct(ResponseInterface $response)
    {
        $this->format = $response->getHeaderLine('X-Clickhouse-Format');
        $this->queryId = $response->getHeaderLine('X-Clickhouse-Query-Id');
        $this->timezone = $response->getHeaderLine('X-Clickhouse-Timezone');

        // Summary
        if ($summary = $response->getHeaderLine('X-Clickhouse-Summary')) {
            $this->summary = new Summary(json_decode($summary, true));
        }

        $body = $response->getBody();
        if ($body->getSize() > 0) {
            $this->stream = $body;

            // Parse Json
            $contentType = $response->getHeaderLine('Content-Type');
            if (str_starts_with(trim($contentType), 'application/json')) {
                $data = json_decode($body->getContents(), true);
                $this->data = $data['data'] ?? null;
                $this->meta = $data['meta'] ?? null;
                $this->rows = $data['rows'] ?? null;
                $this->rowsBeforeLimitAtLeast = $data['rows_before_limit_at_least'] ?? null;
                $this->statistics = $data['statistics'] ?? null;
                $this->totals = $data['totals'] ?? null;
                $this->extremes = $data['extremes'] ?? null;
            }
        }
    }

    public function __get(string $name)
    {
        return property_exists($this, $name) ? $this->{$name} : null;
    }
}
