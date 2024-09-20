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

use Suyar\ClickHouse\Client;

/**
 * @internal
 * @coversNothing
 */
class ExecuteTest extends AbstractTestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $this->client = $this->makeClient();

        // create table
        $execute = $this->client->newExecute();
        $execute->setQuery(
            <<<'QUERY'
CREATE TABLE IF NOT EXISTS test_e
(
    id UInt32,
    name String
)
ENGINE = MergeTree
ORDER BY id;
QUERY
        );
        $this->client->send($execute);
    }

    protected function tearDown(): void
    {
        // drop table
        $execute = $this->client->newExecute();
        $execute->setQuery('DROP TABLE IF EXISTS test_e');
        $this->client->send($execute);
    }

    public function testBaseExecute()
    {
        $sessionId = '11-22-33';

        $params = $this->client->newExecute('CREATE TEMPORARY TABLE IF NOT EXISTS e1 (id UInt32)');
        $params->setCompressRequest(true);
        $params->setSessionId($sessionId);
        $response = $this->client->send($params);

        $params = $this->client->newExecute('INSERT INTO e1 (id) VALUES (1),(2),({three:UInt32})');
        $params->setBindings(['three' => 3]);
        $params->setCompressRequest(true);
        $params->setSessionId($sessionId);
        $response = $this->client->send($params);
        $this->assertEquals(3, $response->summary->written_rows);

        $params = $this->client->newExecute('SELECT * FROM e1 WHERE id in (1, {two:String}) FORMAT JSON');
        $params->setBindings(['two' => 2]);
        $params->setDecompressResponse(true);
        $params->setSessionId($sessionId);
        $response = $this->client->send($params);

        // Other usage is consistent with query and insert.

        $this->assertGreaterThan(1, $response->summary->read_rows);
    }
}
