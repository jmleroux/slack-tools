<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\PhpFilesCache;

class AbstractQuery
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SlackClient
     */
    protected $client;

    public function __construct(SlackClient $client)
    {
        $this->client = $client;
        $this->cache = new PhpFilesCache('slack', 600);
    }
}
