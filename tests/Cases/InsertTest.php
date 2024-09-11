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

/**
 * @internal
 * @coversNothing
 */
class InsertTest extends AbstractTestCase
{
    public function testInsert()
    {
        $client = $this->makeClient();

        $params = $client->newInsert('summtt12');
        $params->setValues([
            [1, '你好好好好好好好好好好好好好好好好好好好好'],
            [2, '我好好好好好好好好好好好好好好好好好好好好'],
            [3, '他好好好好好好好好好好好好好好好好好好好好'],
        ]);
        $params->setColumns(['key', 'value']);

        $response = $client->insert($params);
        $body = $response->getBody();

        var_dump($body->getContents());

        $this->assertTrue(true);
    }

    public function testInsertResource()
    {
        $stream = fopen('php://memory', 'r+');
        for ($f = 1; $f <= 10; ++$f) {
            fwrite($stream, json_encode([$f, '你好好好好好好好好好']) . "\n");
        }
        rewind($stream);

        $client = $this->makeClient();

        $params = $client->newInsert('summtt12');
        $params->setValues($stream);

        $response = $client->insert($params);
        $body = $response->getBody();

        var_dump($body->getContents());

        $this->assertTrue(true);
    }
}
