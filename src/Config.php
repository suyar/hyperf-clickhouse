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

namespace Suyar\ClickHouse;

use function Hyperf\Collection\data_get;

/**
 * @property string $host
 * @property int $port
 * @property string $database
 * @property string $username
 * @property string $password
 * @property bool $https
 * @property bool $compressRequest
 * @property bool $decompressResponse
 * @property int $maxHandles
 * @property array $options
 * @property array $settings
 */
class Config
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config['host'] = (string) data_get($config, 'host', '127.0.0.1');
        $this->config['port'] = (int) data_get($config, 'port', 8123);
        $this->config['database'] = (string) data_get($config, 'database', 'default');
        $this->config['username'] = (string) data_get($config, 'username', 'default');
        $this->config['password'] = (string) data_get($config, 'password', '');
        $this->config['https'] = (bool) data_get($config, 'https', false);
        $this->config['compressRequest'] = (bool) data_get($config, 'compress_request', false);
        $this->config['decompressResponse'] = (bool) data_get($config, 'decompress_response', false);
        $this->config['maxHandles'] = (int) data_get($config, 'max_handles', 10);

        $options = data_get($config, 'options', []);
        $this->config['options'] = is_array($options) ? $options : [];

        $settings = data_get($config, 'settings', []);
        $this->config['settings'] = is_array($settings) ? $settings : [];
    }

    public function __get(string $name)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : null;
    }
}
