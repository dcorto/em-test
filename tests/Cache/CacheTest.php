<?php

namespace App\Tests\Cache;

use App\Cache\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    private $sut;

    public function setUp()
    {
        $this->sut = new Cache();
    }

    public function testCache()
    {
        $data = rand(1,1000);
        $this->sut->put($data);

        $result = $this->sut->get('api.euromillions.results');

        $this->assertEquals($data,$result);
    }

    public function tearDown()
    {
        $this->sut->put(null);
    }
}