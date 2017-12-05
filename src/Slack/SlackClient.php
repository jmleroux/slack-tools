<?php

namespace App\Slack;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class SlackClient
{
    /**
     * @var ClientInterface
     */
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client([
            'base_uri' => 'https://slack.com/api/'
        ]);
    }

    public function post(string $apiToken, string $endpoint, array $queryParams, array $body = null)
    {
        $query = array_merge(
            ['token' => $apiToken,],
            $queryParams
        );

        $options = ['query' => $query];

        if (null !== $body) {
            $options['body'] = json_encode($body);
        }

        $response = $this->guzzle->post($endpoint, $options);

        return $response;
    }
}
