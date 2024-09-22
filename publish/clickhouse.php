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
        // Set [Content-Encoding=gzip] by default and compress the request body.
        'compress_request' => false,
        // Set [enable_http_compression=1] by default and decompress the response body.
        'decompress_response' => false,
        // Guzzle max curl handles.
        'max_handles' => 10,
        // Guzzle default options.
        'options' => [
            'timeout' => 0,
        ],
        // ClickHouse default settings.
        'settings' => [
            // 'max_execution_time' => 30,
        ],
    ],
];
