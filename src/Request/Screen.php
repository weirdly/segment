<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#screen
 */
final class Screen extends AbstractRequest
{
    public const PROPERTY_NAME = 'name';

    use AnonymousAwareTrait, TimestampAwareTrait {
        AnonymousAwareTrait::jsonSerialize as serializeAnonymous;
        TimestampAwareTrait::jsonSerialize as serializeTimestamp;
    }

    private ?string $name = null;

    private array $properties = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Name of the screen See the Name field docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/screen#name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Free-form dictionary of properties of the screen, like name.
     * See the Properties field docs for a list of reserved property names.
     *
     * @see https://segment.com/docs/connections/spec/screen#properties
     */
    public function withProperty(string $property, $value): self
    {
        $this->properties[$property] = $value;

        return $this;
    }

    /**
     * Name of the page. This is reserved for future use.
     */
    public function withNameProperty(string $name): self
    {
        return $this->withProperty(self::PROPERTY_NAME, $name);
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->serializeIdentity(),
            $this->serializeAnonymous(),
            parent::jsonSerialize(),
            array_filter([
                'name' => $this->name,
                'properties' => $this->properties,
            ])
        );
    }
}
