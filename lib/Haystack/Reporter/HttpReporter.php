<?php namespace Haystack\Reporter;

use GuzzleHttp\Client;

class HttpReporter implements ReporterInterface
{
    private $client;

    /**
     * HttpReporter constructor.
     */
    public function __construct($baseUrl, $token)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'apiToken' => $token
            ]
        ]);
    }

    public function reportException(\Exception $exception)
    {
        $this->client->post('events', [
            'form_params' => [
                'title' => $exception->getMessage(),
                'type' => 'exception',
                'level' => 'error',
                'stack_trace' => (string) $exception,
            ],
        ]);
    }
}
