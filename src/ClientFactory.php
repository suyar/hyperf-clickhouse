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

use Hyperf\Contract\ConfigInterface;
use Suyar\ClickHouse\Exception\InvalidArgumentException;

class ClientFactory
{
    protected array $clients = [];

    public function __construct(protected ConfigInterface $config)
    {
    }

    public function __invoke(): Client
    {
        return $this->get('default');
    }

    public function get(string $name): Client
    {
        $client = $this->clients[$name] ?? null;

        if (! $client) {
            $config = $this->config->get('clickhouse.' . $name);
            if (! $config || ! is_array($config)) {
                throw new InvalidArgumentException("Client config '{$name}' is invalid.");
            }

            return $this->clients[$name] = new Client($config);
        }

        return $client;
    }
}
