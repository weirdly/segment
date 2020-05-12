<?php declare(strict_types=1);

namespace Weirdly\Segment\Error;

use Psr\Http\Message\ResponseInterface;
use Weirdly\Segment\Request\AbstractRequest;

final class RequestError extends SegmentError
{
    public static function invalidBatchRequest(AbstractRequest $request): self
    {
        return new static('Cannot batch request of type "%s"', get_class($request));
    }

    public static function invalidJson(\JsonException $error): self
    {
        return new static('Cannot encode JSON', 0, $error);
    }

    public static function serverError(ResponseInterface $response): self
    {
        throw new static('Server error');
    }

    public static function clientError(ResponseInterface $response): self
    {
        throw new static(sprintf('HTTP status %d %s', $response->getStatusCode(), $response->getBody()->getContents()));
    }

    public static function unknownApiError(ResponseInterface $response): self
    {
        throw new static('Could not complete request');
    }
}
