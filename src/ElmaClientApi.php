<?php

declare(strict_types=1);

namespace Glsv\ElmaApi;

use Glsv\ElmaApi\exceptions\ElmaApiException;
use GuzzleHttp\Client;

class ElmaClientApi
{
    protected Client $client;

    public function __construct(protected string $baseUrl, protected string $bearerToken)
    {
        $this->client = new Client(['base_uri' => $baseUrl]);
    }

    public function makePost(string $relativeUrl, array $requestData)
    {
        $response = $this->client->post($relativeUrl, [
            'headers' => ['Authorization' => 'Bearer ' . $this->bearerToken],
            'body' => json_encode($requestData),
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new ElmaApiException(sprintf(
                    'Request %s is fail. Status code: %d', $relativeUrl, $statusCode)
            );
        }

        $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['success'])) {
            throw new ElmaApiException(sprintf(
                    'Response from %s doesn`t contain field success', $relativeUrl)
            );
        }

        if ($data['success'] !== true) {
            throw new ElmaApiException(sprintf(
                    'Field success in response != true for request to %s', $relativeUrl)
            );
        }

        return $data;
    }

    public function makeMultipart(string $relativeUrl, array $requestData)
    {
        $response = $this->client->post($relativeUrl, [
            'headers' => ['Authorization' => 'Bearer ' . $this->bearerToken],
            'multipart' => $requestData,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new ElmaApiException(sprintf(
                    'Request %s is fail. Status code: %d', $relativeUrl, $statusCode)
            );
        }

        $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['success'])) {
            throw new \DomainException(sprintf(
                    'Response from %s doesn`t contain field success', $relativeUrl)
            );
        }

        if ($data['success'] !== true) {
            throw new ElmaApiException(sprintf(
                    'Field success in response != true for request to %s', $relativeUrl)
            );
        }

        return $data;
    }
}