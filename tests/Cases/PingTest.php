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
class PingTest extends AbstractTestCase
{
    public function testPing()
    {
        $client = $this->makeClient();

        $response = $client->ping();
        $body = $response->getBody();

        var_dump($body->getContents());

        $this->assertTrue(true);
    }
}
