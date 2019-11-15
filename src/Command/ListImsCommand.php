<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Command;

use App\Slack\ChannelsQuery;
use App\Slack\SlackClient;
use App\Slack\UsersQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListImsCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:channels:list-private')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->setDescription("List a user's private channels.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $client = new SlackClient($apiToken);
        $channelsQuery = new ChannelsQuery($client);
        $usersQuery = new UsersQuery($client);

        $data = [];
        foreach ($channelsQuery->listPrivate() as $im) {
            $user = $usersQuery->info($im->user);
            $data[] = [
                $im->id,
                $user->id,
                $user->name,
                $user->is_bot ? 'BOT' : '',
            ];
        }

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['Channel ID', 'User ID', 'User Name', 'Is BOT'],
            $data
        );
    }
}
