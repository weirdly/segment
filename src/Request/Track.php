<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#track
 */
final class Track extends AbstractRequest
{
    public const PROPERTY_REVENUE  = 'revenue';
    public const PROPERTY_CURRENCY = 'currency';
    public const PROPERTY_VALUE    = 'value';

    use AnonymousAwareTrait {
        jsonSerialize as serializeAnonymous;
    }

    use TimestampAwareTrait {
        jsonSerialize as serializeTimestamp;
    }

    private string $event;

    private array $properties = [];

    public function __construct(string $event)
    {
        parent::__construct();

        $this->event = $event;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Free-form dictionary of properties of the event, like revenue.
     * See the Properties docs for a list of reserved property names.
     *
     * @see https://segment.com/docs/connections/spec/track#properties
     */
    public function withProperty(string $property, $value): self
    {
        $this->properties[$property] = $value;

        return $this;
    }

    /**
     * Amount of revenue an event resulted in. This should be a decimal value,
     * so a shirt worth $19.99 would result in a revenue of 19.99.
     */
    public function withRevenueProperty(float $revenue): self
    {
        return $this->withProperty(self::PROPERTY_REVENUE, $revenue);
    }

    /**
     * Currency of the revenue an event resulted in This should be sent in the ISO 4127 format.
     * If this is not set, we assume the revenue to be in US dollars.
     */
    public function withCurrencyProperty(string $currency): self
    {
        return $this->withProperty(self::PROPERTY_CURRENCY, $currency);
    }

    /**
     * An abstract “value” to associate with an event. This is typically used in situations where the event
     * doesn’t generate real-dollar revenue, but has an intrinsic value to a marketing team, like newsletter signups.
     */
    public function withValueProperty(float $value): self
    {
        return $this->withProperty(self::PROPERTY_VALUE, $value);
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->serializeAnonymous(),
            $this->serializeTimestamp(),
            parent::jsonSerialize(),
            array_filter([
                'event'      => $this->event,
                'properties' => $this->properties,
            ])
        );
    }
}
