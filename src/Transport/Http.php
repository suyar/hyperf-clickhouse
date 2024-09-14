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

namespace Suyar\ClickHouse\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlFactory;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Suyar\ClickHouse\Config;
use Suyar\ClickHouse\Exception\DatabaseException;
use Suyar\ClickHouse\Exception\TransportException;
use Suyar\ClickHouse\Param\BaseParams;

class Http
{
    protected Client $httpClient;

    public function __construct(protected Config $config)
    {
        $this->initHttpClient();
    }

    public function newRequest(BaseParams $params, string $method = 'POST', string $uri = '/'): Request
    {
        return new Request($this->config, $params, $method, $uri);
    }

    /**
     * @throws TransportException
     * @throws DatabaseException
     */
    public function sendRequest(Request $request): ResponseInterface
    {
        try {
            $response = $this->httpClient->request(
                $request->getMethod(),
                $request->getUri(),
                $request->getOptions()
            );

            if ($response->getStatusCode() !== 200) {
                $body = $response->getBody()->getContents();
                if (preg_match('/Code:\s*\d+\.\s*DB::Exception.+/iu', trim($body), $matches)) {
                    throw new DatabaseException($matches[0]);
                }

                throw new TransportException($response->getBody()->getContents());
            }

            return $response;
        } catch (GuzzleException $e) {
            throw new TransportException($e->getMessage());
        }
    }

    protected function initHttpClient(): void
    {
        $curlFactory = new CurlFactory(max($this->config->maxHandles, 1));
        $handler = new CurlHandler(['handle_factory' => $curlFactory]);
        $stack = HandlerStack::create($handler);

        $host = $this->config->host;
        $port = $this->config->port;

        if ($this->config->https) {
            $baseUri = 'https://' . $host;
            $defaultPort = 443;
        } else {
            $baseUri = 'http://' . $host;
            $defaultPort = 80;
        }

        if ($port > 0 && $port !== $defaultPort) {
            $baseUri = $baseUri . ':' . $port;
        }

        $defaultHeaders = $this->config->options['headers'] ?? [];
        is_array($defaultHeaders) || ($defaultHeaders = []);

        $config = array_replace($this->config->options, [
            'handler' => $stack,
            'base_uri' => $baseUri,
            'http_errors' => false,
            'auth' => [$this->config->username, $this->config->password],
            'headers' => array_replace(
                $defaultHeaders,
                [
                    'User-Agent' => 'suyar/hyperf-clickhouse',
                    'Connection' => 'keep-alive',
                ]
            ),
        ]);

        $this->httpClient = new Client($config);
    }
}
