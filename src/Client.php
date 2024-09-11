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

    public function ping()
    {
        $request = $this->http->newRequest(new PingParams(), 'GET', '/ping');

        return $this->http->sendRequest($request);
    }

    public function query(QueryParams $params)
    {
        $request = $this->http->newRequest($params);

        return $this->http->sendRequest($request);
    }

    public function insert(InsertParams $params)
    {
        $request = $this->http->newRequest($params);

        return $this->http->sendRequest($request);
    }

    public function execute(ExecuteParams $params)
    {
    }
}
