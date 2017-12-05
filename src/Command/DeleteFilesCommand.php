<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Command;

use App\Slack\FilesQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteFilesCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:files:delete')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
            ->addArgument('user', InputArgument::REQUIRED, 'User ID')
            ->setDescription("Delete a user's files.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelId = $input->getArgument('channel');
        $userId = $input->getArgument('user');

        $client = new SlackClient($apiToken);
        $query = new FilesQuery($client);

        $deleted = $query->deleteAll($channelId, $userId);

        foreach ($deleted as $fileId) {
            $output->writeln(sprintf('Deleted file ID = %s', $fileId));
        }
    }
}
