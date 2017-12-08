<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Command;

use App\Exception\ChannelNotFoundException;
use App\Slack\ChannelsQuery;
use App\Slack\MessagesQuery;
use App\Slack\SlackClient;
use App\Slack\UsersQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListMessagesCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:messages:list')
            ->addArgument('api_token', InputArgument::REQUIRED, 'Your API token')
            ->addOption(
                'channel_id',
                null,
                InputOption::VALUE_REQUIRED,
                'Channel ID'
            )
            ->addOption(
                'channel_name',
                null,
                InputOption::VALUE_REQUIRED,
                'Channel name'
            )
            ->setDescription("List messages in a channel.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiToken = $input->getArgument('api_token');
        $channelName = $input->getOption('channel_name');
        $channelId = $input->getOption('channel_id');

        if (null === $channelId && null === $channelName) {
            throw new \RuntimeException('You must specify a channel name or ID.');
        }

        $client = new SlackClient($apiToken);
        $channelsQuery = new ChannelsQuery($client);

        if (null !== $channelName) {
            $channels = $channelsQuery->list();
            $channel = $channelsQuery->filterByName($channels, $channelName);
            if (null === $channel) {
                $usersQuery = new UsersQuery($client);
                $usersQuery->list();
                $channels = array_filter($channelsQuery->listPrivate(), function (\stdClass $channel) use ($channelName, $usersQuery) {
                    $user = $usersQuery->findByName($channelName);
                    if (null === $user) {
                        return false;
                    }
                    $userId = $usersQuery->findByName($channelName)->id;
                    return isset($channel->user) && $channel->user === $userId;
                });
                $channel = reset($channels);
            }
            if (null === $channel || empty($channel)) {
                throw new ChannelNotFoundException($channelName);
            }
            $channelId = $channel->id;
        }

        $messagesQuery = new MessagesQuery($client);
        $messages = $messagesQuery->list($channelId, 1000);

        foreach ($messages as $message) {
            $output->write($message->ts);
            $output->write(' - ');
            if (isset($message->user)) {
                $output->write($message->user);
            }
            $output->write(' - ');
            $output->writeln($message->text);
        }
        $output->writeln(sprintf('End of log for channel ID %s', $channelId));
    }
}
