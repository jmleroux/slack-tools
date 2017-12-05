<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

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

    /**
     * List channels
     *
     * @return \stdClass[]
     */
    public function list(): array
    {
        $response = $this->client->post(
            'channels.list',
            [],
            null
        );

        return $response->channels;
    }

    /**
     * List all private discussions
     *
     * @return \stdClass[]
     */
    public function listPrivate(): array
    {
        $response = $this->client->post(
            'im.list',
            [],
            null
        );

        return $response->ims;
    }

    /**
     * List all messages of a channel
     *
     * @param string $channelId
     * @param int    $limit
     *
     * @return \stdClass[]
     */
    public function history(string $channelId, int $limit = 100): array
    {
        try {
            $response = $this->client->post(
                'channels.history',
                [
                    'channel' => $channelId,
                    'count'   => $limit,
                ],
                null
            );
        } catch (\RuntimeException $e) {
            $response = $this->client->post(
                'im.history',
                [
                    'channel' => $channelId,
                    'count'   => $limit,
                ],
                null
            );
        }

        return $response->messages;
    }
}
