<?php

namespace App\Slack;

class ChannelsQuery extends AbstractQuery
{
    /**
     * @var UsersQuery
     */
    private $users;

    public function __construct(SlackClient $client)
    {
        parent::__construct($client);
        $this->users = new UsersQuery($client);
    }

    public function list(): array
    {
        $response = $this->client->post(
            'channels.list',
            [],
            null
        );

        return $response->channels;
    }

    public function history(string $channelId, int $count = 100): array
    {
        try {
            $response = $this->client->post(
                'channels.history',
                [
                    'channel' => $channelId,
                    'count' => $count,
                ],
                null
            );
        } catch (\RuntimeException $e) {
            $response = $this->client->post(
                'im.history',
                [
                    'channel' => $channelId,
                    'count' => $count,
                ],
                null
            );
        }

        return $response->messages;
    }
}
