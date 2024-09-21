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

namespace Suyar\ClickHouse;

use Psr\Http\Message\StreamInterface;
use Suyar\ClickHouse\Param\ExecuteParams;
use Suyar\ClickHouse\Param\InsertParams;
use Suyar\ClickHouse\Param\PingParams;
use Suyar\ClickHouse\Param\QueryParams;
use Suyar\ClickHouse\Transport\Http;
use Suyar\ClickHouse\Transport\Response;
use Throwable;

class Client
{
    protected Config $config;

    protected Http $http;

    public function __construct(array $config)
    {
        $this->config = new Config($config);
        $this->http = new Http($this->config);
    }

    public function newQuery(string $query = '', array $biddings = []): QueryParams
    {
        return (new QueryParams())->setQuery($query)->setBindings($biddings);
    }

    /**
     * @param array|resource|StreamInterface $values
     */
    public function newInsert(string $table = '', $values = [], array $columns = []): InsertParams
    {
        return (new InsertParams())->setTable($table)->setValues($values)->setColumns($columns);
    }

    public function newExecute(string $query = '', array $biddings = []): ExecuteParams
    {
        return (new ExecuteParams())->setQuery($query)->setBindings($biddings);
    }

    public function ping(bool $silent = false): bool
    {
        try {
            $request = $this->http->newRequest(new PingParams(), 'GET', '/ping');

            $response = $this->http->sendRequest($request);

            return trim($response->getBody()->getContents()) === 'Ok.';
        } catch (Throwable $t) {
            if ($silent) {
                return false;
            }

            throw $t;
        }
    }

    /**
     * Execute statement with params.
     */
    public function send(ExecuteParams|InsertParams|QueryParams $params): Response
    {
        $request = $this->http->newRequest($params);

        return new Response($this->http->sendRequest($request));
    }

    public function showProcesslist(): array
    {
        $response = $this->send($this->newQuery('SHOW PROCESSLIST'));

        return $response?->data[0] ?? [];
    }

    public function showDatabases(): array
    {
        $response = $this->send($this->newQuery('SHOW DATABASES'));

        return array_column($response?->data ?: [], 'name');
    }

    public function showTables(): array
    {
        $response = $this->send($this->newQuery('SHOW TABLES'));

        return array_column($response?->data ?: [], 'name');
    }

    public function showCreateTable(string $table): string
    {
        $response = $this->send($this->newQuery("SHOW CREATE TABLE `{$table}`"));

        return $response?->data[0]['statement'] ?? '';
    }

    public function getDatabaseSize(string $database = ''): array
    {
        $query = <<<'SQL'
SELECT
    database,
    formatReadableSize(sum(data_compressed_bytes) AS size) AS compressed,
    formatReadableSize(sum(data_uncompressed_bytes) AS usize) AS uncompressed,
    round(usize / size, 2) AS compr_rate,
    sum(rows) AS rows,
    count() AS part_count
FROM system.parts
WHERE (active = 1) AND (database LIKE {db:String})
GROUP BY
    database
ORDER BY size DESC;
SQL;

        $response = $this->send($this->newQuery($query, ['db' => "%{$database}%"]));

        return array_column($response?->data ?? [], null, 'database');
    }

    public function getTableSize(string $table = '', string $database = ''): array
    {
        $query = <<<'SQL'
SELECT
    table,
    formatReadableSize(sum(data_compressed_bytes) AS size) AS compressed,
    formatReadableSize(sum(data_uncompressed_bytes) AS usize) AS uncompressed,
    round(usize / size, 2) AS compr_rate,
    sum(rows) AS rows,
    count() AS part_count
FROM system.parts
WHERE (active = 1) AND (database = {db:String}) AND (table LIKE {tb:String})
GROUP BY
    table
ORDER BY size DESC;
SQL;

        $database || ($database = $this->config->database);
        $response = $this->send($this->newQuery($query, ['db' => $database, 'tb' => "%{$table}%"]));

        return array_column($response?->data ?? [], null, 'table');
    }

    public function tableExists(string $table, string $database = ''): bool
    {
        $database || ($database = $this->config->database);
        $query = $this->newQuery("EXISTS TABLE `{$database}`.`{$table}`");

        $response = $this->send($query);

        return ($response?->data[0]['result'] ?? null) === 1;
    }

    public function getServerUptime(): int
    {
        $response = $this->send($this->newQuery('SELECT uptime() as uptime'));

        return $response?->data[0]['uptime'] ?? 0;
    }

    public function getServerVersion(): string
    {
        $response = $this->send($this->newQuery('SELECT version() as version'));

        return $response?->data[0]['version'] ?? '';
    }

    public function getServerTimeZone(): string
    {
        $response = $this->send($this->newQuery('SELECT serverTimeZone() as tz'));

        return $response?->data[0]['tz'] ?? '';
    }

    public function getServerSettings(string $search = ''): array
    {
        $response = $this->send(
            $this->newQuery(
                'SELECT * FROM system.settings' . ($search ? ' WHERE name LIKE {name:String}' : ''),
                ['name' => "{$search}"]
            ),
        );

        return array_column($response?->data ?? [], null, 'name');
    }
}
