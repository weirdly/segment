<?php declare(strict_types=1);

namespace Weirdly\Segment\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function post(string $uri, \JsonSerializable $body): ResponseInterface;
}
