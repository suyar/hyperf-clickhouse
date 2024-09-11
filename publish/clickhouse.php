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
use function Hyperf\Support\env;

return [
    'default' => [
        'host' => (string) env('CLICK_HOUSE_HOST', '127.0.0.1'),
        'port' => (int) env('CLICK_HOUSE_PORT', 8123),
        'username' => (string) env('CLICK_HOUSE_USER', 'default'),
        'password' => (string) env('CLICK_HOUSE_PASS', ''),
        'database' => (string) env('CLICK_HOUSE_DB', 'default'),
        'https' => (bool) env('CLICK_HOUSE_HTTPS', false),
        'compress_request' => false,
        'decompress_response' => false,
        // guzzle max curl handles
        'max_handles' => 10,
        // guzzle default options
        'options' => [
            'timeout' => 30,
        ],
        // clickhouse default query settings
        'settings' => [
            // 'max_execution_time' => 30,
        ],
    ],
];
