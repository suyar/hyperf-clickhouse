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

use Suyar\ClickHouse\Exception\ClickHouseException;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class PingTest extends AbstractTestCase
{
    public function testPingOk()
    {
        $client = $this->makeClient();

        $this->assertTrue($client->ping());
    }

    public function testPingFail()
    {
        $client = $this->makeClient('fail');
        $this->assertFalse($client->ping(true));

        try {
            $client->ping();
        } catch (Throwable $t) {
            $this->assertInstanceOf(ClickHouseException::class, $t);
        }
    }
}
