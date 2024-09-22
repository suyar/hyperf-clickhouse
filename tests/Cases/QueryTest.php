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

namespace Test\Cases;

use GuzzleHttp\Psr7\Utils;
use Suyar\ClickHouse\Client;
use Suyar\ClickHouse\Formats;

/**
 * @internal
 * @coversNothing
 */
class QueryTest extends AbstractTestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $this->client = $this->makeClient();

        // create table
        $execute = $this->client->newExecute();
        $execute->setQuery(
            <<<'QUERY'
CREATE TABLE IF NOT EXISTS test_q
(
    id UInt32,
    name String
)
ENGINE = MergeTree
ORDER BY id;
QUERY
        );
        $this->client->send($execute);

        // insert
        $insert = $this->client->newInsert('test_q');
        $insert->setColumns(['id', 'name']);
        $insert->setValues([
            [1, 'a'],
            [2, 'b'],
            [3, 'c'],
            [4, "a\nd"],
        ]);
        $this->client->send($insert);
    }

    protected function tearDown(): void
    {
        // drop table
        $execute = $this->client->newExecute();
        $execute->setQuery('DROP TABLE IF EXISTS test_q');
        $this->client->send($execute);
    }

    public function testBaseQuery()
    {
        $params = $this->client->newQuery();
        $params->setQuery("select * from test_q where name = 'a'");
        $params->setQueryId('aa-bb-cc');
        $params->setDecompressResponse(true);
        $params->setSetting('max_execution_time', 60);
        $params->setProgress(function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) {
            // do something
        });
        $response = $this->client->send($params);

        $this->assertSame(Formats::JSON, $response->format);
        $this->assertSame('aa-bb-cc', $response->queryId);
        $this->assertIsString($response->timezone);
        $this->assertCount(1, $response->data);
    }

    public function testQueryWithSession()
    {
        $sessionId = 'cc-bb-aa';

        // create table in session
        $execute = $this->client->newExecute('CREATE TEMPORARY TABLE IF NOT EXISTS s1 (id UInt32)');
        $execute->setSessionId($sessionId);
        $this->client->send($execute);

        // insert
        $insert = $this->client->newInsert('s1', [[1], [2], [3]], ['id']);
        $insert->setSessionId($sessionId);
        $this->client->send($insert);

        $query = $this->client->newQuery();
        $query->setQuery('select * from s1');
        $query->setSessionId($sessionId);
        $response = $this->client->send($query);

        $this->assertGreaterThan(1, count($response->data));
    }

    public function testQueryWithBiddings()
    {
        $params = $this->client->newQuery();
        $params->setQuery('select * from test_q where name = {name:String}');
        $params->setBinding('name', "a\nd");
        $response = $this->client->send($params);

        $this->assertCount(1, $response->data);
    }

    public function testQueryWithFormat()
    {
        $params = $this->client->newQuery();
        $params->setQuery('select * from test_q');
        $params->setFormat(Formats::JSONEachRow);
        $response = $this->client->send($params);

        $this->assertStringStartsWith('{', $response->stream->getContents());
    }

    public function testQueryPersistTo()
    {
        $saveTo = BASE_PATH . '/tests/resource/ouput.data';

        $params = $this->client->newQuery();
        $params->setQuery('select * from test_q');
        $params->setFormat(Formats::JSONEachRow);

        // with path
        $params->setPersistTo($saveTo);
        $response = $this->client->send($params);
        $this->assertFileExists($saveTo);

        // with resource
        $params->setPersistTo(fopen($saveTo, 'w+'));
        $response = $this->client->send($params);
        $this->assertFileExists($saveTo);

        // with stream
        $params->setPersistTo(Utils::streamFor(fopen($saveTo, 'w+')));
        $response = $this->client->send($params);
        $this->assertFileExists($saveTo);
    }
}
