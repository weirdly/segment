<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

trait IdentityAwareTrait
{
    /**
     * optional if anonymousID is set instead
     */
    private ?string $userId = null;

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Unique identifier for the user in your database. A userId or an anonymousId is required.
     * See the Identities docs for more details.
     *
     * @see https://segment.com/docs/connections/spec/identify#identities
     */
    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'userId' => $this->userId,
        ]);
    }

    public static function createIdentifiedRequest(string $userId, ...$arguments): self
    {
        $self = new static(...$arguments);
        $self->userId = $userId;

        return $self;
    }
}
