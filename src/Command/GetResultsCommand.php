<?php

// src/Command/GetResultsCommand.php
namespace App\Command;

use App\ApiEuroMillions\ApiEuroMillions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetResultsCommand extends Command
{
    private $apiEuroMillionsService;

    public function __construct(ApiEuroMillions $apiEuroMillionsService)
    {
        $this->apiEuroMillionsService = $apiEuroMillionsService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('euromillions:results')
            ->setDescription('Shows the lasts results from EuroMillions')
            ->setHelp('This is a cool command...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->apiEuroMillionsService->getLastResults();

        $output->writeln('<info>EuroMillions Results</info>');
        if($data){
            $output->writeln('<info>ID:</info>'.$data['lottery_id']);
            $output->writeln('<info>Date: </info>'.$data['date']);
            $output->writeln('<info>Results: </info>'.$this->formatResults($data));
            $output->writeln('<info>Jackpot: </info>'.$data['jackpot_amount'].' '.$data['jackpot_currency']);
        }
        else {
            $output->writeln('<error>NO EuroMillons Results!</error>');
        }
    }

    private function formatResults($data){
        return $data['one'].", ".$data['two'].", ".$data['three'].", ".$data['four'].", ".$data['five']." <bg=yellow;options=bold>".$data['lucky_one']." ".$data['lucky_two']."</>";
    }
}