<?php

namespace App\Slack;

class MessagesQuery extends AbstractQuery
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

    public function list(string $channelId, int $count = 100): array
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

    public function deleteAll(string $channelId, string $userId, int $count = 100): array
    {
        $messages = array_reverse($this->list($channelId, 1000));

        $deleted = [];
        foreach ($messages as $message) {
            if ($message->user === $userId) {
                $deleted[] = $this->delete($channelId, $message->ts);
            }
            if (count($deleted) >= $count) {
                break;
            }
        }

        return $deleted;
    }

    public function delete($channelId, $ts): string
    {
        $this->client->post(
            'chat.delete',
            [
                'channel' => $channelId,
                'ts' => $ts,
            ],
            null
        );

        return $ts;
    }
}
