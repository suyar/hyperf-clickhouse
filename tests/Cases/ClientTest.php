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
class ClientTest extends AbstractTestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $this->client = $this->makeClient();

        // create table
        $execute = $this->client->newExecute();
        $execute->setQuery(
            <<<'QUERY'
CREATE TABLE IF NOT EXISTS test_u
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
        $execute->setQuery('DROP TABLE IF EXISTS test_u');
        $this->client->send($execute);
    }

    public function testShowProcesslist()
    {
        \Hyperf\Coroutine\go(function () {
            $query = $this->client->newQuery('SELECT number,sleep(0.5) FROM system.numbers limit 5');
            $this->client->send($query);
        });

        $processlist = $this->client->showProcesslist();

        $this->assertIsArray($processlist);
    }

    public function testShowDatabases()
    {
        $databases = $this->client->showDatabases();

        $this->assertIsArray($databases);
    }

    public function testShowTables()
    {
        $tables = $this->client->showTables();

        $this->assertIsArray($tables);
    }

    public function testShowCreateTable()
    {
        $sql = $this->client->showCreateTable('test_u');

        $this->assertStringStartsWith('CREATE TABLE', $sql);
    }

    public function testGetDatabaseSize()
    {
        $sizeInfo = $this->client->getDatabaseSize('default');

        $this->assertArrayHasKey('default', $sizeInfo);
    }

    public function testGetTableSize()
    {
        $this->client->send(
            $this->client->newInsert(
                'test_u',
                [
                    [1, 'name1'],
                    [2, 'name2'],
                    [3, 'name3'],
                ],
                ['id', 'name']
            )
        );

        $sizeInfo = $this->client->getTableSize('test_u');

        $this->assertArrayHasKey('test_u', $sizeInfo);
    }

    public function testTableExists()
    {
        $exists = $this->client->tableExists('test_u');

        $this->assertTrue($exists);
    }

    public function testGetServerUptime()
    {
        $second = $this->client->getServerUptime();

        $this->assertGreaterThan(0, $second);
    }

    public function testGetServerVersion()
    {
        $version = $this->client->getServerVersion();

        $this->assertStringContainsString('.', $version);
    }

    public function testGetServerTimeZone()
    {
        $tz = $this->client->getServerTimeZone();

        $this->assertIsString($tz);
    }

    public function testGetServerSettings()
    {
        $settings = $this->client->getServerSettings('min_compress_block_size');

        $this->assertArrayHasKey('min_compress_block_size', $settings);
    }
}
