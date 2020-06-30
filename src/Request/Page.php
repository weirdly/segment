<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#page
 */
final class Page extends AbstractRequest
{
    public const PROPERTY_NAME     = 'name';
    public const PROPERTY_PATH     = 'path';
    public const PROPERTY_REFERRER = 'referrer';
    public const PROPERTY_SEARCH   = 'search';
    public const PROPERTY_TITLE    = 'title';
    public const PROPERTY_URL      = 'url';
    public const PROPERTY_KEYWORDS = 'keywords';

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
     * Name of the page For example, most sites have a “Signup” page that can be useful to tag,
     * so you can see users as they move through your funnel.
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Free-form dictionary of properties of the page, like url and referrer
     * See the Properties field docs for a list of reserved property names.
     *
     * @see https://segment.com/docs/connections/spec/page#properties
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

    /**
     * Path portion of the URL of the page.
     *
     * Equivalent to canonical path which defaults to location.pathname from the DOM API.
     */
    public function withPathProperty(string $path): self
    {
        return $this->withProperty(self::PROPERTY_PATH, $path);
    }

    /**
     * Full URL of the previous page.
     *
     * Equivalent to document.referrer from the DOM API.
     */
    public function withReferrerProperty(string $referrer): self
    {
        return $this->withProperty(self::PROPERTY_REFERRER, $referrer);
    }

    /**
     * Query string portion of the URL of the page.
     *
     * Equivalent to location.search from the DOM API.
     */
    public function withSearchProperty(string $search): self
    {
        return $this->withProperty(self::PROPERTY_SEARCH, $search);
    }

    /**
     * Title of the page.
     *
     * Equivalent to document.title from the DOM API.
     */
    public function withTitleProperty(string $title): self
    {
        return $this->withProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     * Full URL of the page.
     *
     * First we look for the canonical url. If the canonical url is not provided, we use location.href from the DOM API.
     */
    public function withUrlProperty(string $url): self
    {
        return $this->withProperty(self::PROPERTY_URL, $url);
    }

    /**
     * A list/array of keywords describing the content of the page.
     *
     * The keywords would most likely be the same as, or similar to, the keywords you would find in an html meta tag
     * for SEO purposes. This property is mainly used by content publishers that rely heavily on pageview tracking.
     * This is not automatically collected.
     */
    public function withKeywordProperty(string $keyword, $value): self
    {
        $keywords           = $this->properties[self::PROPERTY_KEYWORDS] ?? [];
        $keywords[$keyword] = $value;

        return $this->withProperty(self::PROPERTY_KEYWORDS, $keywords);
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
            self::filterDict([
                'name'       => $this->name,
                'properties' => $this->properties,
            ])
        );
    }
}
