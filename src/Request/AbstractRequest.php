<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

abstract class AbstractRequest implements \JsonSerializable
{
    public const CONTEXT_ACTIVE     = 'active';
    public const CONTEXT_APP        = 'app';
    public const CONTEXT_CAMPAIGN   = 'campaign';
    public const CONTEXT_DEVICE     = 'device';
    public const CONTEXT_IP         = 'ip';
    public const CONTEXT_LIBRARY    = 'library';
    public const CONTEXT_LOCALE     = 'locale';
    public const CONTEXT_LOCATION   = 'location';
    public const CONTEXT_NETWORK    = 'network';
    public const CONTEXT_OS         = 'os';
    public const CONTEXT_PAGE       = 'page';
    public const CONTEXT_REFERRER   = 'referrer';
    public const CONTEXT_SCREEN     = 'screen';
    public const CONTEXT_TIMEZONE   = 'timezone';
    public const CONTEXT_GROUP_ID   = 'groupId';
    public const CONTEXT_TRAITS     = 'traits';
    public const CONTEXT_USER_AGENT = 'userAgent';

    public const INTEGRATION_ALL = 'All';

    /**
     * Dictionary of extra information that provides useful context about a message,
     * but is not directly related to the API call like ip address or locale.
     * See the Context field docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/common#context
     */
    private array $context = [];

    /**
     * A dictionary of destination names that the message should be sent to. 'All' is
     * a special key that applies when no key for a specific destination is found.
     *
     * @see https://segment.com/docs/connections/spec/common/#integrations
     */
    private array $integrations = [];

    public function __construct()
    {
        $libraryContext = [
            'name'    => 'weirdly/segment',
            'version' => '?',
        ];

        if (class_exists(\Jean85\PrettyVersions::class)) {
            $libraryContext['version'] = \Jean85\PrettyVersions::getVersion('weirdly/segment')->getPrettyVersion();
        }

        $this->withLibraryContext($libraryContext);

        if (method_exists($this, 'setTimestamp')) {
            $this->setTimestamp(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        }
    }

    /**
     * Whether a user is active.
     *
     * This is usually used to flag an .identify() call to just update the traits but not “last seen.”
     */
    public function withActiveContext(bool $active): self
    {
        return $this->withContext(self::CONTEXT_ACTIVE, $active);
    }

    /**
     * Dictionary of information about the current application, containing name, version and build.
     * This is collected automatically from our mobile libraries when possible.
     */
    public function withAppContext(array $app): self
    {
        return $this->withContext(self::CONTEXT_APP, $app);
    }

    /**
     * Dictionary of information about the campaign that resulted in the API call, containing name, source, medium, term and content.
     *
     * This maps directly to the common UTM campaign parameters.
     */
    public function withCampaignContext(array $campaign): self
    {
        return $this->withContext(self::CONTEXT_CAMPAIGN, $campaign);
    }

    /**
     * Dictionary of information about the device, containing id, advertisingId, manufacturer, model, name, type and version.
     */
    public function withDeviceContext(array $device): self
    {
        return $this->withContext(self::CONTEXT_DEVICE, $device);
    }

    /**
     * Current user’s IP address.
     */
    public function withIpContext(string $ip): self
    {
        return $this->withContext(self::CONTEXT_IP, $ip);
    }

    /**
     * Dictionary of information about the library making the requests to the API, containing name and version.
     */
    public function withLibraryContext(array $library): self
    {
        return $this->withContext(self::CONTEXT_LIBRARY, $library);
    }

    /**
     * Locale string for the current user, for example en-US.
     */
    public function withLocaleContext(string $locale): self
    {
        return $this->withContext(self::CONTEXT_LOCALE, $locale);
    }

    /**
     * Dictionary of information about the user’s current location, containing city, country, latitude, longitude, region and speed.
     */
    public function withLocationContext(array $location): self
    {
        return $this->withContext(self::CONTEXT_LOCATION, $location);
    }

    /**
     * Dictionary of information about the current network connection, containing bluetooth, carrier, cellular and wifi
     */
    public function withNetworkContext(array $network): self
    {
        return $this->withContext(self::CONTEXT_NETWORK, $network);
    }

    /**
     * Dictionary of information about the operating system, containing name and version
     */
    public function withOsContext(array $os): self
    {
        return $this->withContext(self::CONTEXT_OS, $os);
    }

    /**
     * Dictionary of information about the current page in the browser, containing hash, path, referrer, search,
     * title and url. This is automatically collected by Analytics.js.
     */
    public function withPageContext(array $page): self
    {
        return $this->withContext(self::CONTEXT_PAGE, $page);
    }

    /**
     * Dictionary of information about the way the user was referred to the website or app, containing type, name, url and link
     */
    public function withReferrerContext(array $referrer): self
    {
        return $this->withContext(self::CONTEXT_REFERRER, $referrer);
    }

    /**
     * Dictionary of information about the device’s screen, containing density, height and width
     */
    public function withScreenContext(array $screen): self
    {
        return $this->withContext(self::CONTEXT_SCREEN, $screen);
    }

    /**
     * Timezones are sent as tzdata strings to add user timezone information which might be stripped from the
     * timestamp, for example America/New_York
     */
    public function withTimezoneContext(string $timezone): self
    {
        return $this->withContext(self::CONTEXT_TIMEZONE, $timezone);
    }

    /**
     * Group / Account ID.
     *
     * This is useful in B2B use cases where you need to attribute your non-group calls to a company or account. It is relied on by several Customer Success and CRM tools.
     */
    public function withGroupIdContext(string $groupId): self
    {
        return $this->withContext(self::CONTEXT_GROUP_ID, $groupId);
    }

    /**
     * Dictionary of traits of the current user
     *
     * This is useful in cases where you need to track an event, but also associate information from a previous
     * identify call. You should fill this object the same way you would fill traits in an identify call.
     */
    public function withTraitsContext(array $traits): self
    {
        return $this->withContext(self::CONTEXT_TRAITS, $traits);
    }

    /**
     * User agent of the device making the request
     */
    public function withUserAgentContext(string $userAgent): self
    {
        return $this->withContext(self::CONTEXT_USER_AGENT, $userAgent);
    }

    /**
     * Dictionary of destinations to either enable or disable.
     * See the Destinations field docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/common#integrations
     */
    public function withIntegration(string $integration, bool $value): self
    {
        $this->integrations[$integration] = $value;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function enableAllIntegrations(): self
    {
        return $this->withIntegration(self::INTEGRATION_ALL, true);
    }

    public function disableAllIntegrations(): self
    {
        return $this->withIntegration(self::INTEGRATION_ALL, false);
    }

    public function getIntegrations(): array
    {
        return $this->integrations;
    }

    public function jsonSerialize(): array
    {
        return self::filterDict([
            'context'      => self::filterDict($this->context),
            'integrations' => self::filterDict($this->integrations),
        ]);
    }

    private function withContext(string $field, $value): self
    {
        $this->context[$field] = $value;

        return $this;
    }

    public static function filterDict(array $dict): array
    {
        return array_filter($dict, fn ($val) => (!is_array($val) && $val !== null && $val !== '') || (is_array($val) && !empty($val)));
    }
}
