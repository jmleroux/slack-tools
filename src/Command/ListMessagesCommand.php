<?php

namespace App\Command;

use App\Slack\ChannelsQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListMessagesCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:channel:history')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
            ->setDescription("List channels.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelId = $input->getArgument('channel');
        $client = new SlackClient($apiToken);
        $query = new ChannelsQuery($client);
        $messages = $query->history($channelId, 1000);

        foreach($messages as $message) {
            $output->write($message->ts);
            $output->write(' - ');
            if (isset($message->user)) {
                $output->write($message->user);
            }
            $output->write(' - ');
            $output->writeln($message->text);
        }
    }
}
