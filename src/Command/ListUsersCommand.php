<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Command;

use App\Slack\SlackClient;
use App\Slack\UsersQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListUsersCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:users:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->setDescription("List all users.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');

        $client = new SlackClient($apiToken);
        $query = new UsersQuery($client);

        $users = $query->list();

        $data = [];
        foreach ($users as $user) {
            $data[] = [$user->id, $user->name, $user->is_bot ? 'BOT' : ''];
        };

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['User ID', 'User Name', 'Is BOT'],
            $data
        );

    }
}
