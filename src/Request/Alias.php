<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#alias
 */
final class Alias extends AbstractRequest
{
    use AnonymousAwareTrait {
        jsonSerialize as serializeAnonymous;
    }

    use TimestampAwareTrait {
        jsonSerialize as serializeTimestamp;
    }

    private string $previousId;

    public function __construct(string $previousId)
    {
        $this->previousId = $previousId;
    }

    /**
     * Previous unique identifier for the user.
     *
     * See the Previous ID field docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/alias#previous-id
     */
    public function getPreviousId(): string
    {
        return $this->previousId;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->serializeAnonymous(),
            $this->serializeTimestamp(),
            parent::jsonSerialize(),
            array_filter([
                'previousId' => $this->previousId,
            ])
        );
    }
}
