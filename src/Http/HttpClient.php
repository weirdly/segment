<?php declare(strict_types=1);

namespace Weirdly\Segment\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Weirdly\Segment\Error\RequestError;

class HttpClient
{
    private string $writeKey;

    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(string $writeKey, ClientInterface $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->writeKey       = $writeKey;
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory  = $streamFactory;
    }

    public function post(string $uri, \JsonSerializable $body): ResponseInterface
    {
        try {
            $content = $this->encodeJson($body);
        } catch (\JsonException $exception) {
            throw RequestError::invalidJson($exception);
        }

        $request = $this->requestFactory->createRequest('POST', $uri)
            ->withAddedHeader('Authorization', $this->getAuthorizationHeader())
            ->withAddedHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($content));

        return $this->client->sendRequest($request);
    }

    private function getAuthorizationHeader(): string
    {
        $credentials = sprintf('%s:', $this->writeKey);

        return sprintf('Basic %s', base64_encode($credentials));
    }

    /**
     * @throws \JsonException
     */
    private function encodeJson(\JsonSerializable $json): string
    {
        return json_encode($json, JSON_THROW_ON_ERROR);
    }
}
