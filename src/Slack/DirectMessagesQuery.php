<?php

namespace App\Slack;

class DirectMessagesQuery extends AbstractQuery
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
            'im.list',
            [],
            null
        );

        $ims = [];
        foreach ($response->ims as $im) {
            if (!$im->is_user_deleted) {
                $ims[$im->id] = $im->user;
            }
        }

        $directMessagesList = [];
        foreach ($ims as $imId => $userId) {
            $directMessagesList[$imId] = $this->users->info($userId);
        }

        return $directMessagesList;
    }
}
