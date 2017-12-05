<?php

namespace App\Command;

use App\Slack\MessagesQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteMessageCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:message:delete')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
            ->addArgument('ts', InputArgument::REQUIRED, 'File timestamp')
            ->setDescription("Delete a message.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelId = $input->getArgument('channel');
        $ts = $input->getArgument('ts');

        $client = new SlackClient($apiToken);
        $query = new MessagesQuery($client);

        $deleted = $query->delete($channelId, $ts);

        $output->writeln(sprintf('Deleted message TS = %s', $deleted));
    }
}
