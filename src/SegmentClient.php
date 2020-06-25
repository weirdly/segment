<?php declare(strict_types=1);

namespace Weirdly\Segment;

use Weirdly\Segment\Error\RequestError;
use Weirdly\Segment\Http\HttpClientInterface;
use Weirdly\Segment\Request;

class SegmentClient
{
    private const RESOURCE_IDENTIFY = 'identify';
    private const RESOURCE_TRACK    = 'track';
    private const RESOURCE_PAGE     = 'page';
    private const RESOURCE_SCREEN   = 'screen';
    private const RESOURCE_GROUP    = 'group';
    private const RESOURCE_ALIAS    = 'alias';
    private const RESOURCE_BATCH    = 'batch';

    private const VERSION_DEFAULT = self::VERSION_V1;
    private const VERSION_V1      = 'v1';

    private static array $resourceMap = [
        Request\Identify::class => self::RESOURCE_IDENTIFY,
        Request\Track::class    => self::RESOURCE_TRACK,
        Request\Page::class     => self::RESOURCE_PAGE,
        Request\Screen::class   => self::RESOURCE_SCREEN,
        Request\Group::class    => self::RESOURCE_GROUP,
        Request\Alias::class    => self::RESOURCE_ALIAS,
        Request\Batch::class    => self::RESOURCE_BATCH,
    ];

    private HttpClientInterface $client;

    private string $version;

    public function __construct(HttpClientInterface $client, string $version = self::VERSION_DEFAULT)
    {
        $this->client  = $client;
        $this->version = $version;
    }

    public function identify(string $userId, array $traits = [], bool $anonymous = false): void
    {
        $request = Request\Identify::create($userId, $anonymous);

        foreach ($traits as $trait => $value) {
            $request->withTrait($trait, $value);
        }

        $this->send($request);
    }

    public function track(string $userId, string $event, array $properties = [], $anonymous = false): void
    {
        $request = Request\Track::create($userId, $anonymous, $event);

        foreach ($properties as $property => $value) {
            $request->withProperty($property, $value);
        }

        $this->send($request);
    }

    public function page(string $userId, string $name, bool $anonymous = false): void
    {
        $request = Request\Page::create($userId, $anonymous)
            ->setName($name);

        $this->send($request);
    }

    public function screen(string $userId, string $name, bool $anonymous = false): void
    {
        $request = Request\Screen::create($userId, $anonymous)
            ->setName($name);

        $this->send($request);
    }

    public function group(string $userId, string $groupId, array $traits = [], $anonymous = true): void
    {
        $request = Request\Group::create($userId, $anonymous, $groupId);

        foreach ($traits as $trait => $value) {
            $request->withTrait($trait, $value);
        }

        $this->send($request);
    }

    public function alias(string $userId, string $previousId): void
    {
        $request = Request\Alias::createIdentifiedRequest($userId, $previousId);

        $this->send($request);
    }

    public function batch(...$requests): void
    {
        $request = new Request\Batch();

        array_walk($requests, [$request, 'addRequest']);

        $this->send($request);
    }

    public function send(Request\AbstractRequest $segmentRequest): void
    {
        $endpoint = self::$resourceMap[get_class($segmentRequest)];

        $segmentRequest->withLibraryContext([
            'name'    => 'weirdly/segment',
            'version' => '0.3', // What is a better way to set this?
        ]);

        $response = $this->client->post($this->buildUri($endpoint), $segmentRequest);

        if ($response->getStatusCode() >= 500) {
            throw RequestError::serverError($response);
        }

        if ($response->getStatusCode() >= 400) {
            throw RequestError::clientError($response);
        }

        if (200 !== $response->getStatusCode()) {
            throw RequestError::unknownApiError($response);
        }
    }

    private function buildUri(string $resource): string
    {
        return sprintf('https://api.segment.io/%s/%s', $this->version, $resource);
    }
}
