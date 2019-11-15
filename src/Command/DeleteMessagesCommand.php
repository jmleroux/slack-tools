<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Command;

use App\Slack\MessagesQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteMessagesCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:messages:delete-all')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
            ->addArgument('user', InputArgument::REQUIRED, 'User ID')
            ->addArgument('count', InputArgument::OPTIONAL, 'Max number of messages to delete.', 100)
            ->setDescription("Delete all user's messages in a channel.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelId = $input->getArgument('channel');
        $userId = $input->getArgument('user');
        $count = (int) $input->getArgument('count');

        $client = new SlackClient($apiToken);
        $query = new MessagesQuery($client);

        $deleted = $query->deleteAll($channelId, $userId, $count);

        foreach ($deleted as $messageTs) {
            $output->writeln(sprintf('Deleted message TS = %s', $messageTs));
        }
        $output->writeln(sprintf('Deleted messages count = %d', count($deleted)));
    }
}
