<?php

namespace App\Command;

use App\Slack\SlackClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListImsCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this->setName('app:im:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->setDescription("List a user's files.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $apiToken = $input->getArgument('api_token');
        $client = new SlackClient();

        $response = $client->post(
            $apiToken,
            'im.list',
            [],
            null
        );
        $rawData = \GuzzleHttp\json_decode($response->getBody()->getContents());

        $ims = [];
        foreach ($rawData->ims as $im) {
            if (!$im->is_user_deleted) {
                $ims[$im->id] = $im->user;
            }
        }

        foreach ($ims as $imId => $userId) {
            $this->printUserInfo($client, $apiToken, $imId, $userId);
        }
    }

    private function printUserInfo(SlackClient $client, string $apiToken, string $imId, string $userId)
    {
        $response = $client->post(
            $apiToken,
            'users.info',
            ['user' => $userId],
            null
        );
        $rawData = \GuzzleHttp\json_decode($response->getBody()->getContents());
        $pattern = 'Channel ID = %s - User ID = %s - %s';
        if ($rawData->user->is_bot) {
            $pattern = '[BOT] ' . $pattern;
        }
        $this->output->writeln(sprintf(
            $pattern,
            $imId,
            $rawData->user->id,
            $rawData->user->name
        ));
    }
}
