<?php

namespace App\Command;

use App\Slack\DirectMessagesQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListImsCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:im:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->setDescription("List a user's files.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $client = new SlackClient($apiToken);
        $directMessages = new DirectMessagesQuery($client);

        foreach ($directMessages->list() as $imId => $user) {
            $pattern = 'Channel ID = %s - User ID = %s - %s';
            if ($user->is_bot) {
                $pattern = '[BOT] ' . $pattern;
            }
            $output->writeln(sprintf(
                $pattern,
                $imId,
                $user->id,
                $user->name
            ));
        }
    }
}
