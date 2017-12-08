<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

class UsersQuery extends AbstractQuery
{
    const CACHE_USER_ID = 'userId.%s';
    const CACHE_USER_NAME = 'userName.%s';

    /**
     * List all users
     *
     * @return \stdClass[]
     */
    public function list(): array
    {
        $response = $this->client->post(
            'users.list',
            [],
            null
        );

        foreach ($response->members as $user) {
            $cacheKey = sprintf(self::CACHE_USER_ID, $user->id);
            $this->cache->set($cacheKey, $user);
            $cacheKey = sprintf(self::CACHE_USER_NAME, $user->name);
            $this->cache->set($cacheKey, $user);
        }

        return $response->members;
    }

    /**
     * Find a user
     *
     * @param string $name
     *
     * @return \stdClass|null
     */
    public function findByName(string $name)
    {
        $cacheKey = sprintf(self::CACHE_USER_NAME, $name);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        return null;
    }

    /**
     * Find user's info
     *
     * @param string $userId
     *
     * @return \stdClass
     */
    public function info(string $userId): \stdClass
    {
        $cacheKey = sprintf(self::CACHE_USER_ID, $userId);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $response = $this->client->post(
            'users.info',
            ['user' => $userId],
            null
        );

        $info = $response->user;

        $this->cache->set($cacheKey, $info);

        return $info;
    }
}
