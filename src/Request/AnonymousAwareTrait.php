<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

trait AnonymousAwareTrait
{
    use IdentityAwareTrait {
        jsonSerialize as serializeIdentity;
    }

    /**
     * optional if userID is set instead
     */
    private ?string $anonymousId = null;

    public function getAnonymousId(): ?string
    {
        return $this->anonymousId;
    }

    /**
     * A pseudo-unique substitute for a User ID, for cases when you don’t have an absolutely unique identifier.
     * A userId or an anonymousId is required.
     * See the Identities docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/identify#identities
     */
    public function setAnonymousId(?string $anonymousId): self
    {
        $this->anonymousId = $anonymousId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->serializeIdentity(),
            self::filterDict([
                'anonymousId' => $this->anonymousId,
            ])
        );
    }

    public static function createAnonymousRequest(string $anonymousId, ...$arguments): self
    {
        $self = new static(...$arguments);
        $self->anonymousId = $anonymousId;

        return $self;
    }

    public static function create(string $userId, bool $anonymous, ...$arguments): self
    {
        if ($anonymous) {
            return self::createAnonymousRequest($userId, ...$arguments);
        }

        return self::createIdentifiedRequest($userId, ...$arguments);
    }
}
