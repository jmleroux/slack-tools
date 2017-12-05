<?php

namespace App\Command;

use App\Slack\FilesQuery;
use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteFileCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:file:delete')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('file_id', InputArgument::REQUIRED, 'File ID')
            ->setDescription("Delete a file.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $fileId = $input->getArgument('file_id');

        $client = new SlackClient($apiToken);
        $query = new FilesQuery($client);

        $fileId = $query->delete($fileId);

        $output->writeln(sprintf('Deleted file ID = %s', $fileId));
    }
}
