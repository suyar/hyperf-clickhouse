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

use DateTimeInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use Suyar\ClickHouse\Config;
use Suyar\ClickHouse\Exception\InvalidArgumentException;
use Suyar\ClickHouse\Formats;
use Suyar\ClickHouse\Param\BaseParams;
use Suyar\ClickHouse\Param\ExecuteParams;
use Suyar\ClickHouse\Param\InsertParams;
use Suyar\ClickHouse\Param\PingParams;
use Suyar\ClickHouse\Param\QueryParams;
use Throwable;

class Request
{
    protected array $options = [];

    public function __construct(
        protected Config $config,
        BaseParams $params,
        protected string $method = 'POST',
        protected string $uri = '/'
    ) {
        $this->options = match (true) {
            $params instanceof PingParams => $this->buildPingOptions($params),
            $params instanceof QueryParams => $this->buildQueryOptions($params),
            $params instanceof InsertParams => $this->buildInsertOptions($params),
            $params instanceof ExecuteParams => $this->buildExecuteOptions($params),
            default => throw new InvalidArgumentException('Unsupported param type.'),
        };
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function buildPingOptions(PingParams $params): array
    {
        return [];
    }

    protected function buildQueryOptions(QueryParams $params): array
    {
        $options = [];

        $options['body'] = $this->formatQuery($params->getQuery(), $params->getFormat());

        $settings = array_replace($this->config->settings, $params->getSettings());
        if ($params->getDecompressResponse() ?? $this->config->decompressResponse) {
            $settings['enable_http_compression'] = 1;
        }

        if (intval($settings['enable_http_compression'] ?? 0) === 1) {
            $options['headers']['Accept-Encoding'] = 'gzip';
        }

        $options['query'] = $this->formatSearch(
            $this->config->database,
            '',
            $params->getBindings(),
            $settings,
            $params->getSessionId(),
            $params->getQueryId()
        );

        if ($sink = $params->getPersistTo()) {
            $options['sink'] = $this->formatSink($sink);
        }

        return $options;
    }

    protected function buildInsertOptions(InsertParams $params): array
    {
        $options = [];

        $format = $params->getFormat() ?: Formats::JSONCompactEachRow;

        $body = $this->formatValues($params->getValues(), $format, $params->getStringAsFile());

        if ($params->getCompressRequest() ?? $this->config->compressRequest) {
            $body = $this->compressRequest($body);
            $options['headers']['Content-Encoding'] = 'gzip';
        }

        $options['body'] = $body;

        $options['query'] = $this->formatSearch(
            $this->config->database,
            $this->getInsertQuery($params->getTable(), $params->getColumns(), $params->getExcept(), $format),
            $params->getBindings(),
            array_replace($this->config->settings, $params->getSettings()),
            $params->getSessionId(),
            $params->getQueryId()
        );

        return $options;
    }

    protected function buildExecuteOptions(ExecuteParams $params): array
    {
        $options = [];

        $query = rtrim(trim($params->getQuery()), ';');

        if ($values = $params->getValues()) {
            $format = $this->getFormatFromQuery($query) ?: Formats::JSONCompactEachRow;
            $body = $this->formatValues($values, $format, $params->getStringAsFile());

            if ($params->getCompressRequest() ?? $this->config->compressRequest) {
                $body = $this->compressRequest($body);
                $options['headers']['Content-Encoding'] = 'gzip';
            }

            $hasValues = true;
        } else {
            $body = $query;

            $hasValues = false;
        }

        $options['body'] = $body;

        $settings = array_replace($this->config->settings, $params->getSettings());
        if ($params->getDecompressResponse() ?? $this->config->decompressResponse) {
            $settings['enable_http_compression'] = 1;
        }

        if (intval($settings['enable_http_compression'] ?? 0) === 1) {
            $options['headers']['Accept-Encoding'] = 'gzip';
        }

        $options['query'] = $this->formatSearch(
            $this->config->database,
            $hasValues ? $query : '',
            $params->getBindings(),
            $settings,
            $params->getSessionId(),
            $params->getQueryId()
        );

        if ($sink = $params->getPersistTo()) {
            $options['sink'] = $this->formatSink($sink);
        }

        return $options;
    }

    /**
     * @param resource|StreamInterface|string $sink
     * @return resource|StreamInterface
     * @throws InvalidArgumentException
     */
    protected function formatSink($sink)
    {
        try {
            if (is_string($sink)) {
                return Utils::tryFopen($sink, 'w+');
            }

            if (
                is_resource($sink) && get_resource_type($sink) === 'stream'
                || $sink instanceof StreamInterface
            ) {
                return $sink;
            }

            throw new InvalidArgumentException('Unsupported persist type.');
        } catch (Throwable $t) {
            throw new InvalidArgumentException($t->getMessage());
        }
    }

    protected function getFormatFromQuery(string $query): string
    {
        if (preg_match('/\s+FORMAT\s+(\w+)\s*$/ius', $query, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param resource|StreamInterface|string $body
     * @return resource|string
     * @throws InvalidArgumentException
     */
    protected function compressRequest($body)
    {
        if ($body instanceof StreamInterface) {
            throw new InvalidArgumentException('StreamInterface values conflicts with compress_request.');
        }

        if (is_resource($body)) {
            // @see https://www.php.net/manual/zh/filters.compression.php
            // @see https://www.zlib.net/manual.html#Advanced
            stream_filter_append($body, 'zlib.deflate', STREAM_FILTER_READ, ['window' => 30]);

            return $body;
        }

        if (is_string($body)) {
            return gzdeflate($body, -1, ZLIB_ENCODING_GZIP);
        }

        return $body;
    }

    protected function getInsertQuery(string $table, array $columns, bool $except, string $format): string
    {
        $columnsStr = '';

        if ($columns) {
            if ($except) {
                $columnsStr = ' (* EXCEPT(' . implode(', ', $columns) . '))';
            } else {
                $columnsStr = ' (' . implode(', ', $columns) . ')';
            }
        }

        return "INSERT INTO {$table}{$columnsStr} FORMAT {$format}";
    }

    /**
     * @param array|resource|StreamInterface|string $values
     * @return resource|StreamInterface|string
     * @throws InvalidArgumentException
     */
    protected function formatValues($values, string $format, bool $stringAsFile = false)
    {
        if (! $values) {
            return new InvalidArgumentException('The value cannot be empty.');
        }

        try {
            if ($values instanceof StreamInterface) {
                return $values;
            }

            if (is_string($values)) {
                return $stringAsFile ? Utils::tryFopen($values, 'r') : $values;
            }

            if (is_resource($values) && get_resource_type($values) === 'stream') {
                return $values;
            }

            if (is_array($values)) {
                // e.g. JSONEachRow JSONStringsEachRow JSONCompactEachRow JSONCompactStringsEachRow ...
                $values = array_map(
                    fn ($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    $values
                );

                return implode("\n", $values);
            }

            throw new InvalidArgumentException('Unsupported value type.');
        } catch (Throwable $t) {
            throw new InvalidArgumentException($t->getMessage());
        }
    }

    protected function formatQuery(string $query, string $format): string
    {
        $query = rtrim(trim($query), ';');
        $format || ($format = Formats::JSON);
        $formatStr = "\nFORMAT {$format}";

        if (preg_match('/\s+(FORMAT\s+\w+)\s*$/ius', $query)) {
            return preg_replace('/\s+(FORMAT\s+\w+)\s*$/ius', '', $query) . $formatStr;
        }

        return $query . $formatStr;
    }

    protected function formatSearch(
        string $database,
        string $query,
        array $biddings,
        array $settings,
        string $sessionId,
        string $queryId
    ): array {
        return array_replace(
            $queryId ? ['query_id' => $queryId] : [],
            $this->formatBiddings($biddings),
            $this->formatSettings($settings),
            ['database' => $database],
            $query ? ['query' => $query] : [],
            $sessionId ? ['session_id' => $sessionId] : [],
        );
    }

    protected function formatBiddings(array $biddings): array
    {
        $keyValues = [];

        foreach ($biddings as $key => $value) {
            $keyValues["param_{$key}"] = $this->formatBiddingValue($value);
        }

        return $keyValues;
    }

    protected function formatBiddingValue(mixed $value, $wrapStringInQuotes = false): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_string($value)) {
            $result = '';
            foreach (mb_str_split($value, 1, 'UTF-8') as $str) {
                $result .= match ($str) {
                    "\n" => '\n',
                    "\t" => '\t',
                    "\r" => '\r',
                    "'" => "\\'",
                    '\\' => '\\\\',
                    default => $str,
                };
            }

            return $wrapStringInQuotes ? "'{$result}'" : $result;
        }

        if (is_array($value)) {
            $formatted = array_map(fn ($v) => $this->formatBiddingValue($v, true), $value);

            return '[' . implode(',', $formatted) . ']';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return strval($value);
    }

    protected function formatSettings(array $settings): array
    {
        $keyValues = [];

        foreach ($settings as $key => $value) {
            $keyValues[$key] = $this->formatSettingValue($value);
        }

        return $keyValues;
    }

    protected function formatSettingValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return strval($value);
    }
}
