<?php

namespace App\Tests\ApiEuroMillions;

use App\ApiEuroMillions\ApiEuroMillions;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ApiEuroMillionsTest extends TestCase
{

    private $sut;
    private $connectionService;
    private $cacheService;

    public function setUp()
    {
        $this->connectionService = $this->prophesize('Doctrine\DBAL\Driver\Connection');
        $this->cacheService = $this->prophesize('App\Cache\Cache');

        $this->sut = new ApiEuroMillions(
            $this->connectionService->reveal(),
            $this->cacheService->reveal()
        );
    }

    public function testGetLastResultsWithNoExistingData()
    {
        $mockedStatement = $this->prophesize('\Doctrine\DBAL\Driver\Statement');
        $this->connectionService->query(Argument::containingString('COUNT('))->willReturn($mockedStatement->reveal());
        $mockedStatement->fetch()->willReturn(array('count' => '0'));

        $mockedStatement = $this->prophesize('\Doctrine\DBAL\Driver\Statement');
        $this->connectionService->exec(Argument::containingString('INSERT'))->willReturn($mockedStatement->reveal());

        $result = $this->sut->getLastResults();

        $this->assertNotNull($result);
        $this->assertArrayHasKey('lottery_id',$result);
        $this->assertArrayHasKey('date',$result);
        $this->assertArrayHasKey('one',$result);
        $this->assertArrayHasKey('two',$result);
        $this->assertArrayHasKey('three',$result);
        $this->assertArrayHasKey('four',$result);
        $this->assertArrayHasKey('five',$result);
        $this->assertArrayHasKey('lucky_one',$result);
        $this->assertArrayHasKey('lucky_two',$result);
        $this->assertArrayHasKey('jackpot_amount',$result);
        $this->assertArrayHasKey('jackpot_currency',$result);
    }

    public function testGetLastResultsWithExistingData()
    {
        $mockedStatement = $this->prophesize('\Doctrine\DBAL\Driver\Statement');
        $this->connectionService->query(Argument::containingString('COUNT('))->willReturn($mockedStatement->reveal());
        $mockedStatement->fetch()->willReturn(array('count' => '1'));

        $mockedStatement = $this->prophesize('\Doctrine\DBAL\Driver\Statement');
        $this->connectionService->query(Argument::containingString('SELECT'))->willReturn($mockedStatement->reveal());
        $mockedStatement->fetch()->willReturn(array(
            'lottery_id' => '1',
            'draw_date' => '2018-08-31',
            'result_regular_number_one' => '1',
            'result_regular_number_two' => '2',
            'result_regular_number_three' => '3',
            'result_regular_number_four' => '4',
            'result_regular_number_five' => '5',
            'result_lucky_number_one' => '6',
            'result_regular_number_two' => '7',
            'jackpot_amount' => '123312',
            'jackpot_currency_name' => 'EUR',
            )
        );

        $result = $this->sut->getLastResults();

        $this->assertNotNull($result);
        $this->assertArrayHasKey('lottery_id',$result);
        $this->assertArrayHasKey('date',$result);
        $this->assertArrayHasKey('one',$result);
        $this->assertArrayHasKey('two',$result);
        $this->assertArrayHasKey('three',$result);
        $this->assertArrayHasKey('four',$result);
        $this->assertArrayHasKey('five',$result);
        $this->assertArrayHasKey('lucky_one',$result);
        $this->assertArrayHasKey('lucky_two',$result);
        $this->assertArrayHasKey('jackpot_amount',$result);
        $this->assertArrayHasKey('jackpot_currency',$result);
    }

    public function testFetch()
    {
        $result = $this->sut->fetch();

        $this->assertNotNull($result);
        $this->assertArrayHasKey('lottery_id',$result);
        $this->assertArrayHasKey('date',$result);
        $this->assertArrayHasKey('one',$result);
        $this->assertArrayHasKey('two',$result);
        $this->assertArrayHasKey('three',$result);
        $this->assertArrayHasKey('four',$result);
        $this->assertArrayHasKey('five',$result);
        $this->assertArrayHasKey('lucky_one',$result);
        $this->assertArrayHasKey('lucky_two',$result);
        $this->assertArrayHasKey('jackpot_amount',$result);
        $this->assertArrayHasKey('jackpot_currency',$result);
    }
    public function testParseFromApi()
    {

        $fromApi = array(
            'error' => 0,
            'draw' => '2018-01-01',
            'results' => '1,2,3,4,5,6,7',
        );

        $result = $this->sut->parseResponseFromApi($fromApi);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('lottery_id',$result);
        $this->assertArrayHasKey('date',$result);
        $this->assertArrayHasKey('one',$result);
        $this->assertArrayHasKey('two',$result);
        $this->assertArrayHasKey('three',$result);
        $this->assertArrayHasKey('four',$result);
        $this->assertArrayHasKey('five',$result);
        $this->assertArrayHasKey('lucky_one',$result);
        $this->assertArrayHasKey('lucky_two',$result);
        $this->assertArrayHasKey('jackpot_amount',$result);
        $this->assertArrayHasKey('jackpot_currency',$result);
    }
}