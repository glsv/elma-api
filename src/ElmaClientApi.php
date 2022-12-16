<?php

declare(strict_types=1);

namespace Glsv\ElmaApi;

use Glsv\ElmaApi\exceptions\{ElmaApiException, ElmaApiInvalidParamsException, ElmaApiRuntimeException};
use GuzzleHttp\Client;

class ElmaClientApi
{
    protected Client $client;

    /**
     * @throws ElmaApiInvalidParamsException
     */
    public function __construct(protected string $baseUrl, protected string $bearerToken, ?Client $client = null)
    {
        if ($this->baseUrl === "") {
            throw new ElmaApiInvalidParamsException("baseUrl is empty.");
        }

        if ($this->bearerToken === "") {
            throw new ElmaApiInvalidParamsException("bearerToken is empty.");
        }

        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new Client(['base_uri' => $baseUrl]);
        }
    }

    public function makePost(string $relativeUrl, array $requestData)
    {
        $response = $this->client->post($relativeUrl, [
            'headers' => ['Authorization' => 'Bearer ' . $this->bearerToken],
            'body' => json_encode($requestData),
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new ElmaApiRuntimeException(sprintf(
                    'Request %s is fail. Status code: %d', $relativeUrl, $statusCode)
            );
        }

        try {
            $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $err) {
            throw new ElmaApiRuntimeException("Error decode response as json", 0, $err);
        }

        if (!isset($data['success'])) {
            throw new ElmaApiRuntimeException(sprintf(
                    'Response from %s doesn`t contain field success', $relativeUrl)
            );
        }

        if ($data['success'] !== true) {
            throw new ElmaApiRuntimeException(sprintf(
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
            throw new ElmaApiRuntimeException(sprintf(
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
}