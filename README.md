# ClickHouse http client for Hyperf.

[![Latest Stable Version](https://img.shields.io/packagist/v/suyar/hyperf-clickhouse)](https://packagist.org/packages/suyar/hyperf-clickhouse)
[![Total Downloads](https://img.shields.io/packagist/dt/suyar/hyperf-clickhouse)](https://packagist.org/packages/suyar/hyperf-clickhouse)
[![License](https://img.shields.io/packagist/l/suyar/hyperf-clickhouse)](https://github.com/suyar/hyperf-clickhouse)

## Installation

Requirements:

- php: >=8.1
- ext-zlib: *
- ext-swoole: >=5.0 (SWOOLE_HOOK_NATIVE_CURL)
- Composer >= 2.0

```shell
composer require suyar/hyperf-clickhouse
```

## Usage

Publish the files of the clickhouse component:

```shell
php bin/hyperf.php vendor:publish suyar/hyperf-clickhouse
```

Modify your config file `config/autoload/clickhouse.php`:

```php
<?php

declare(strict_types=1);

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
```

Using the `default` connection by `[Inject]`:

```php
namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Suyar\ClickHouse\Client;

class IndexController
{
    #[Inject]
    protected Client $client;

    public function index()
    {
        return $this->client->getServerVersion();
    }
}
```

Or use factory:

```php
namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Suyar\ClickHouse\ClientFactory;

class IndexController
{
    #[Inject]
    protected ClientFactory $clientFactory;

    public function index()
    {
        $client = $this->clientFactory->get('connectionName');

        return $client->getServerVersion();
    }
}
```

### Ping

```php
/** @var \Suyar\ClickHouse\Client $client */

// Returns true or throw exception.
$client->ping();

// Return true or false.
$client->ping(true);
```

### Query

```php
/** @var \Suyar\ClickHouse\Client $client */
/** @var \Suyar\ClickHouse\Param\QueryParams $params */

$params = $client->newQuery('SELECT * FROM table_name');

// It can also be set up in this way.
$params->setQuery('SELECT * FROM table_name');

// Response format, default 'JSON'.
$params->setFormat(\Suyar\ClickHouse\Formats::JSON);

// Set [enable_http_compression=1] and decompress response (gzip only).
$params->setDecompressResponse(true);

// Persistent response body to file via path.
$params->setPersistTo('/path/to/file');

// Persistent response body to file via resource.
$params->setPersistTo(fopen('/path/to/file', 'w+'));

// Persistent response body to file via Psr\Http\Message\StreamInterface.
$params->setPersistTo(\GuzzleHttp\Psr7\Utils::streamFor(fopen('/path/to/file', 'w+')));

// Request with query params.
$response = $client->send($params);
```

### Insert

```php
/** @var \Suyar\ClickHouse\Client $client */
/** @var \Suyar\ClickHouse\Param\InsertParams $params */

$params = $client->newInsert(
    // table name
    'table_name',
    // values
    [
        [1, 'name1'],
        [2, 'name2'],
    ],
    // columns
    ['id', 'name']
);

// Set table name.
$params->setTable('table_name');

// Set columns.
$params->setColumns(['id']);

// Set columns is except.
// INSERT INTO table_name (* EXCEPT(id)) VALUES ('name1'),('name2')
$params->setExcept(true);

// Request format, default 'JSONEachRow'.
$params->setFormat(\Suyar\ClickHouse\Formats::JSONEachRow);

// Set [Content-Encoding=gzip] by default and compress the request body via gzip.
$params->setCompressRequest(true);

// Set values with array.
$params->setValues([['name1'], ['name2'], ['name3']]);

// Set values with string.
$params->setValues(
<<<ROWS
{"id":1,"name":"a"}
{"id":2,"name":"b"}
{"id":3,"name":"c"}
ROWS
);

// Set values with file.
$params->setValues('/path/to/file', true);

// Set values with resource
$params->setValues(fopen('/path/to/file', 'r+'));

// Set values with Psr\Http\Message\StreamInterface.
$params->setValues(\GuzzleHttp\Psr7\Utils::streamFor(fopen('/path/to/file', 'r+')));

// Request with insert params.
$response = $client->send($params);
```

### Execute

```php
/** @var \Suyar\ClickHouse\Client $client */
/** @var \Suyar\ClickHouse\Param\ExecuteParams $params */

$params = $client->newExecute(
<<<SQL
CREATE TABLE IF NOT EXISTS table_name
(
    id UInt32,
    name String
)
ENGINE = MergeTree
ORDER BY id;
SQL
);

// Request with execute params.
$response = $client->send($params);
```

> Execute is the same as Query and Insert, the difference is that you need to manually set the format in the sql.

### Common Parameter

```php
/** @var \Suyar\ClickHouse\Param\QueryParams|\Suyar\ClickHouse\Param\InsertParams|\Suyar\ClickHouse\Param\ExecuteParams $params */

// Set default database.
// Overrides the default values in the configuration file.
$params->setDatabase('default');

// Set progress callback.
$params->setProgress(function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) {
    // do something
});

// Set binddings.
$params->setQuery('SELECT * FROM table_name WHERE name={name:String}');
$params->setQuery('INSERT INTO table_name (id,name) VALUES (1, {name:String})');
$params->setBinding('namne', 'name1');
$params->setBindings(['name' => 'name1']);

// Set ClickHouse settings in query params.
$params->setSetting('max_execution_time', 30);
$params->setSettings(['max_execution_time' => 30])

// Set query_id.
$params->setQueryId('query_id');

// Set session_id.
$params->setSessionId('session_id');
```

### Client Methods

```php
/** @var \Suyar\ClickHouse\Client $client */

// Request with params.
$client->send();

// Show Processlist.
$client->showProcesslist();

// Show databases.
$client->showDatabases();

// Show tables.
$client->showTables();

// Show create table.
$client->showCreateTable();

// Get database size.
$client->getDatabaseSize();

// Get table size.
$client->getTableSize();

// Check table exists.
$client->tableExists();

// Get server uptime.
$client->getServerUptime();

// Get server version.
$client->getServerVersion();

// Get server timezone.
$client->getServerTimeZone();

// Get server settings.
$client->getServerSettings();
```

### Response

```php
/** @var \Suyar\ClickHouse\Transport\Response $response */

// Receive from response header 'X-Clickhouse-Format'
/** @var string $format */
$format = $response->format;

// Receive from response header 'X-Clickhouse-Query-Id'
/** @var string $queryId */
$queryId = $response->queryId;

// Receive from response header 'X-Clickhouse-Timezone'
/** @var string $timezone */
$timezone = $response->timezone;

// Returns null if the response header does not exist 'X-Clickhouse-Summary'.
/** @var \Suyar\ClickHouse\Transport\Summary|null $summary */
$summary = $response->summary;

// Returns null if the response body is empty.
/** @var \Psr\Http\Message\StreamInterface|null $stream */
$stream = $response->stream;

// Returns null if the response body is not "application/json".
/** @var array|null $data */
$data = $response->data;
/** @var array|null $meta */
$meta = $response->meta;
/** @var int|array $rows */
$rows = $response->rows;
/** @var int|null $rowsBeforeLimitAtLeast */
$rowsBeforeLimitAtLeast = $response->rowsBeforeLimitAtLeast;
/** @var array|null $statistics */
$statistics = $response->statistics;
/** @var array|null $totals */
$totals = $response->totals;
/** @var array|null $extremes */
$extremes = $response->extremes;
```

## Via JetBrains

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/suyar)

## Contact

- [Email](mailto:su@zorzz.com)

## License

[MIT](LICENSE)

## Donate 🍵

If you are using this program or like it, you can support me in the following ways:

- Star、Fork、Watch 🚀
- WechatPay、AliPay ❤

|                                        WechatPay                                         |                                       AliPay                                        |
|:----------------------------------------------------------------------------------------:|:-----------------------------------------------------------------------------------:|
|   <img src="https://ooo.0x0.ooo/2024/07/10/OPsOGq.png" alt="Wechat QRcode" width=170>    | <img src="https://ooo.0x0.ooo/2024/07/10/OPsMev.png" alt="AliPay QRcode" width=170> |
