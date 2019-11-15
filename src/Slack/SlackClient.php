<?php
/**
 * @author  JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

use App\Exception\SlackApiErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class SlackClient
{
    /**
     * @var ClientInterface
     */
    private $guzzle;

    /**
     * Legacy token
     * @see https://api.slack.com/custom-integrations/legacy-tokens
     *
     * @var string
     */
    private $apiToken;

    public function __construct(string $apiToken)
    {
        $this->guzzle = new Client([
            'base_uri' => 'https://slack.com/api/',
        ]);
        $this->apiToken = $apiToken;
    }

    public function post(string $endpoint, array $queryParams, array $body = null): \stdClass
    {
        $query = array_merge(
            ['token' => $this->apiToken,],
            $queryParams
        );

        $options = ['query' => $query];

        if (null !== $body) {
            $options['body'] = json_encode($body);
        }

        $response = $this->guzzle->post($endpoint, $options);

        $body = \GuzzleHttp\json_decode($response->getBody()->getContents());

        if (!$body->ok) {
            throw new SlackApiErrorException($body->error);
        }

        return $body;
    }
}
