<?php

namespace App\Command;

use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteFilesCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

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
        $this->output = $output;

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
            $this->delete($client, $apiToken, $file->id);
        }
    }

    private function delete(SlackClient $client, string $apiToken, string $fileId)
    {
        $response = $client->post(
            $apiToken,
            'files.delete',
            ['file' => $fileId],
            null
        );

        $rawData = \GuzzleHttp\json_decode($response->getBody()->getContents());

        if ($rawData->ok) {
            $this->output->writeln(sprintf('Deleted file ID = %s', $fileId));
        }
    }
}
