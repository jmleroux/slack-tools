<?php

namespace App\Command;

use App\Slack\ChannelsQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListChannelsCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:channels:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->setDescription("List channels.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $client = new SlackClient($apiToken);
        $query = new ChannelsQuery($client);
        $channels = $query->list();

        foreach($channels as $channel) {
            $output->write($channel->id);
            $output->write(' - ');
            if ($channel->is_archived) {
                $output->write('(archived) ');
            }
            $output->writeln($channel->name);
        }
    }
}
