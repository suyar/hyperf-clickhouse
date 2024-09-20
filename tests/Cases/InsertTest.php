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
class InsertTest extends AbstractTestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $this->client = $this->makeClient();

        // create table
        $execute = $this->client->newExecute();
        $execute->setQuery(
            <<<'QUERY'
CREATE TABLE IF NOT EXISTS test_i
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
        $execute->setQuery('DROP TABLE IF EXISTS test_i');
        $this->client->send($execute);
    }

    public function testBaseInsert()
    {
        $params = $this->client->newInsert(
            'test_i',
            [
                [1, 'name1'],
                [2, 'name2'],
                [3, 'name3'],
            ],
            ['id', 'name']
        );
        $params->setCompressRequest(true);
        $response = $this->client->send($params);

        $this->assertEquals(3, $response->summary->written_rows);
    }

    public function testInsertWithString()
    {
        $params = $this->client->newInsert('test_i');
        $params->setColumns(['id', 'name']);
        $params->setFormat(Formats::JSONEachRow);
        $params->setValues(
            <<<'VALUES'
{"id":1,"name":"a"}
{"id":2,"name":"b"}
{"id":3,"name":"c"}
VALUES,
            false
        );
        $response = $this->client->send($params);

        $this->assertEquals(3, $response->summary->written_rows);
    }

    public function testInsertWithFile()
    {
        $file = BASE_PATH . '/tests/resource/input.data';

        $input = <<<'INPUT'
{"id":1,"name":"a"}
{"id":2,"name":"b"}
{"id":3,"name":"c"}
INPUT;

        file_put_contents($file, $input);

        $params = $this->client->newInsert('test_i');
        $params->setColumns(['id', 'name']);
        $params->setFormat(Formats::JSONEachRow);

        // with path
        $params->setCompressRequest(true);
        $params->setValues($file, true);
        $response = $this->client->send($params);
        $this->assertEquals(3, $response->summary->written_rows);

        // with resource
        $params->setCompressRequest(true);
        $params->setValues(fopen($file, 'r'));
        $response = $this->client->send($params);
        $this->assertEquals(3, $response->summary->written_rows);

        // with stream
        $params->setCompressRequest(false); // StreamInterface values conflicts with compress_request.
        $params->setValues(Utils::streamFor(fopen($file, 'r')));
        $response = $this->client->send($params);
        $this->assertEquals(3, $response->summary->written_rows);
    }
}
