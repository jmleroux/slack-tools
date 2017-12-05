<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

class UsersQuery extends AbstractQuery
{
    const CACHE_USER_INFO = 'user.%s';

    /**
     * Find user's info
     *
     * @param string $userId
     *
     * @return \stdClass
     */
    public function info(string $userId): \stdClass
    {
        $cacheKey = sprintf(self::CACHE_USER_INFO, $userId);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $response = $this->client->post(
            'users.info',
            ['user' => $userId],
            null
        );

        $info = $response->user;
        unset($info->profile);

        $this->cache->set($cacheKey, $info);

        return $info;
    }
}
