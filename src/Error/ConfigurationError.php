<?php declare(strict_types=1);

namespace Weirdly\Segment\Error;

final class ConfigurationError extends SegmentError
{
    public static function noAvailableClients(): self
    {
        throw new self('Unable to create a client without a compatible client installed.');
    }
}
