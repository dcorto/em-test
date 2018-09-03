<?php

namespace App\ApiEuroMillions;

use App\Interfaces\IResultApi;
use App\Cache\Cache;
use Doctrine\DBAL\Driver\Connection;

class ApiEuroMillions implements IResultApi
{

    const ENDPOINT = 'https://www.magayo.com/api/results.php?api_key=Qs538dw5akaBasBmLd&game=euromillions';
    const ENDPOINT_FAILBACK = 'https://fakeapi.com/api/';

    private $databaseConnection;
    private $cacheService;

    /**
     * ApiEuroMillions constructor.
     *
     * @param Connection $connection
     * @param Cache $cache
     */
    public function __construct(Connection $connection, Cache $cache)
    {
        $this->databaseConnection = $connection;
        $this->cacheService = $cache;
    }

    /**
     * Gets array of results from API / DataBase
     */
    public function getLastResults()
    {
        $results = null;

        $resultsFromCache = $this->cacheService->get('api.euromillions.results');

        if(null === $resultsFromCache) {
            $resultsFromApi = $this->fetch();

            if ($this->checkIfExists(new \DateTime($resultsFromApi['date']))) {
                $results = $this->load();
            } else {
                $this->save($resultsFromApi);
                $results = $resultsFromApi;
            }
            $this->cacheService->put($results);
        }
        else {
            $results = $resultsFromCache;
        }

        return $results;
    }

    /**
     * Gets data directly from API of FallBack API
     *
     * @return array|null
     */
    public function fetch()
    {
        $data = null;

        $json = file_get_contents($this->getEndpoint()); //TODO: Use Guzzle instead a simple file_get_contents

        if(!$json){ //Alternative fetch (fake) if main request fails
            $fakeDateTime = new \DateTime();
            $json = json_encode( //Fake data
                array(
                    'error' => 0,
                    'draw' => $fakeDateTime->format('Y-m-d'),
                    'results' => "04,30,31,38,42,04,06"
                )
            );
        }
        $data = json_decode($json, true);
        $data = $this->parseResponseFromApi($data);
        return $data;
    }

    /**
     * Parses the response from the API for manipulate
     *
     * @param array $fromApi
     * @return array
     */
    public function parseResponseFromApi(Array $fromApi)
    {
        $dateTime = new \DateTime($fromApi['draw']);
        $values = explode(',', $fromApi['results']);

        $parsed = array(
            'lottery_id' => rand(1,1000),
            'date' => $dateTime->format('Y-m-d'),
            'one' => (int) $values[0],
            'two' => (int) $values[1],
            'three' => (int) $values[2],
            'four' => (int) $values[3],
            'five' => (int) $values[4],
            'lucky_one' => (int) $values[5],
            'lucky_two' => (int) $values[6],
            'jackpot_amount' => rand(1,10000000), //TODO: Random for this use case
            'jackpot_currency' => 'EUR', //TODO: I assume that for this use case

        );

        return $parsed;
    }

    /**
     * Stores to Database
     *
     * TODO: Move to a new class with all databases operations
     *
     * @param array $data
     */
    private function save(Array $data)
    {
        $sql = 'INSERT INTO euromillions_draws (
          lottery_id, 
          draw_date, 
          result_regular_number_one, 
          result_regular_number_two, 
          result_regular_number_three, 
          result_regular_number_four, 
          result_regular_number_five, 
          result_lucky_number_one, 
          result_lucky_number_two, 
          jackpot_amount, 
          jackpot_currency_name
          ) VALUES (
            "'.$data['lottery_id'].'",
            "'.$data['date'].'",
            '.$data['one'].',
            '.$data['two'].',
            '.$data['three'].',
            '.$data['four'].',
            '.$data['five'].',
            '.$data['lucky_one'].',
            '.$data['lucky_two'].',
            '.$data['jackpot_amount'].',
            "'.$data['jackpot_currency'].'"            
          )';
        $this->databaseConnection->exec($sql);
    }

    /**
     * Load last result from database
     *
     * TODO: Move to a new class with all databases operations
     *
     * @return array|null
     */
    private function load(){
        $data = null;
        $result = $this->databaseConnection->query('SELECT * FROM euromillions_draws ORDER BY draw_date DESC LIMIT 1')->fetch();

        if($result){
            $data = array(
                'lottery_id' => (int) $result['lottery_id'],
                'date' => $result['draw_date'],
                'one' => (int) $result['result_regular_number_one'],
                'two' => (int) $result['result_regular_number_two'],
                'three' => (int) $result['result_regular_number_three'],
                'four' => (int) $result['result_regular_number_four'],
                'five' => (int) $result['result_regular_number_five'],
                'lucky_one' => (int) $result['result_lucky_number_one'],
                'lucky_two' => (int) $result['result_regular_number_two'],
                'jackpot_amount' => (int) $result['jackpot_amount'],
                'jackpot_currency' => $result['jackpot_currency_name'],
            );
        }

        return $data;
    }

    /**
     * Checks if the given datetime is already on the database
     *
     *  TODO: Move to a new class with all databases operations
     *
     * @param \DateTime $datetime
     * @return bool
     */
    private function checkIfExists(\DateTime $datetime)
    {
        $sql = "SELECT COUNT(id) as count from euromillions_draws WHERE draw_date = '".$datetime->format('Y-m-d')."'";

        $result = $this->databaseConnection->query($sql)->fetch();

        if($result['count'] === "0"){
            return false;
        }

        return true;
    }

    /**
     * Gets the API Endpoint
     *
     * @return string
     */
    private function getEndpoint()
    {
        return self::ENDPOINT;
    }
}