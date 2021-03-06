<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

use App\Exception\SlackApiErrorException;
use GuzzleHttp\Exception\RequestException;

class MessagesQuery extends AbstractQuery
{
    /**
     * List all messages (with limit) in a channel
     *
     * @param string $channelId
     * @param int    $limit
     *
     * @return \stdClass[]
     */
    public function list(string $channelId, int $limit = 100): array
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
        } catch (SlackApiErrorException $e) {
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

    /**
     * Delete all user's messages (with limit) in a channel
     *
     * @param string $channelId
     * @param string $userId
     * @param int    $limit
     *
     * @return \stdClass[]
     */
    public function deleteAll(string $channelId, string $userId, int $limit = 100): array
    {
        $messages = array_reverse($this->list($channelId, 1000));

        $deleted = [];
        foreach ($messages as $message) {
            try {
                // TODO: to improve
                if (!isset($message->user)) {
                    continue;
                }
                if ($message->user === $userId) {
                    $deleted[] = $this->delete($channelId, $message->ts);
                }
                if (count($deleted) >= $limit) {
                    break;
                }
            } catch (SlackApiErrorException $e) {
                break;
            } catch (RequestException $e) {
                if (429 === $e->getCode()) {
                    $halt = (int) $e->getResponse()->getHeader('Retry-After')[0];
                    sleep($halt + 1);
                } else {
                    throw new SlackApiErrorException($e->getMessage());
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete one message in a channel
     *
     * @param $channelId
     * @param $ts
     *
     * @return string
     */
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
