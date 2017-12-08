<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Exception;

use RuntimeException;

class ChannelNotFoundException extends RuntimeException
{
    /**
     * @var string
     */
    protected $message = 'Cannot find unique channel named %s.';

    public function __construct(string $channelId)
    {
        parent::__construct($this->message, $this->code, null);
        $this->message = sprintf($this->message, $channelId);
    }
}
