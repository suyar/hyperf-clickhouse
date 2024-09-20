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

use PHPUnit\Framework\TestCase;
use Suyar\ClickHouse\Client;

/**
 * @internal
 * @coversNothing
 */
class AbstractTestCase extends TestCase
{
    protected const CONFIG = [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 18123,
            'username' => 'default',
            'password' => '',
            'database' => 'default',
            'https' => false,
            'compress_request' => true,
            'decompress_response' => true,
            'max_handles' => 10,
            'options' => [
                'timeout' => 30,
                'debug' => false,
            ],
            'settings' => [
                'max_execution_time' => 30,
            ],
        ],
        'fail' => [
            'host' => '127.0.0.1',
            'port' => 28123,
            'username' => 'default',
            'password' => '',
            'database' => 'default',
            'https' => false,
            'compress_request' => true,
            'decompress_response' => true,
            'max_handles' => 10,
            'options' => [
                'timeout' => 30,
                'debug' => false,
            ],
            'settings' => [
                'max_execution_time' => 30,
            ],
        ],
    ];

    final protected function makeClient(string $name = 'default'): Client
    {
        if (isset(self::CONFIG[$name])) {
            return new Client(self::CONFIG[$name]);
        }

        return new Client(self::CONFIG['default']);
    }
}
