<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

use Weirdly\Segment\Error\RequestError;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#batch
 */
final class Batch extends AbstractRequest
{
    private static array $mappedTypes = [
        Group::class    => 'group',
        Identify::class => 'identify',
        Page::class     => 'page',
        Screen::class   => 'screen',
        Track::class    => 'track',
    ];

    /**
     * An array of identify, group, track, page and screen method calls.
     */
    private array $batch = [];

    public function addRequest(AbstractRequest $request): self
    {
        $requestClass = get_class($request);

        if (!array_key_exists($requestClass, self::$mappedTypes)) {
            throw RequestError::invalidBatchRequest($request);
        }

        $this->batch[] = $request;

        return $this;
    }

    public function getBatch(): array
    {
        return $this->batch;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'batch' => array_map(fn($request) => array_merge(['type' => self::$mappedTypes[get_class($request)]], $request->jsonSerialize()), $this->batch),
            ]
        );
    }
}
