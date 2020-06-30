<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#historical-import
 */
trait TimestampAwareTrait
{
    private ?\DateTimeInterface $timestamp = null;

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * Timestamp when the message itself took place, defaulted to the current time by the Segment Tracking API,
     * as a ISO-8601 format date string. If the event just happened, leave it out and we’ll use the server’s time.
     * If you’re importing data from the past, make sure you to provide a timestamp.
     * See the Timestamps fields docs for more detail.
     *
     * @see https://segment.com/docs/connections/spec/common#timestamps
     */
    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return self::filterDict([
            'timestamp' => $this->timestamp
                ? $this->timestamp->format(\DateTimeInterface::ATOM)
                : null,
        ]);
    }
}
