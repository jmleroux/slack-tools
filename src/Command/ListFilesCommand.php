<?php

namespace App\Command;

use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListFilesCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:files:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
            ->addArgument('user', InputArgument::REQUIRED, 'User ID')
            ->setDescription("List files a user's files.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelId = $input->getArgument('channel');
        $userId = $input->getArgument('user');

        $client = new SlackClient();

        $response = $client->post(
            $apiToken,
            'files.list',
            ['channel' => $channelId, 'user' => $userId],
            null
        );
        $rawData = \GuzzleHttp\json_decode($response->getBody()->getContents());

        foreach ($rawData->files as $file) {
            $output->writeln(sprintf('%s - %s', $file->id, $file->name));
        }
    }
}
