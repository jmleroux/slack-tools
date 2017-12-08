<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

use App\Exception\ChannelNotFoundException;

class ChannelsQuery extends AbstractQuery
{
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
     * @param \stdClass[] $channels
     * @param string      $name
     *
     * @return \stdClass|null
     */
    public function filterByName(array $channels, string $name)
    {
        $channel = array_filter($channels, function (\stdClass $channel) use ($name) {
            return $channel->name == $name;
        });

        if (count($channel) === 0) {
            return null;
        }
        if (count($channel) !== 1) {
            throw new ChannelNotFoundException($name);
        }

        return reset($channel);
    }
}
