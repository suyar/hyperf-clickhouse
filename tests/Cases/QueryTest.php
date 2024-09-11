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
class QueryTest extends AbstractTestCase
{
    public function testQuery()
    {
        $client = $this->makeClient();

        $params = $client->newQuery();
        $params->setQuery('select * from summtt12 where value = {value:String}');
        $params->setBidding('value', "a\nb");
        $params->setFormat('JSON');

        $response = $client->query($params);
        $body = $response->getBody();

        var_dump($body->getContents());

        $this->assertTrue(true);
    }
}
