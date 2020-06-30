<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#group
 */
final class Group extends AbstractRequest
{
    public const TRAIT_ADDRESS     = 'address';
    public const TRAIT_AVATAR      = 'avatar';
    public const TRAIT_CREATED_AT  = 'createdAt';
    public const TRAIT_DESCRIPTION = 'description';
    public const TRAIT_EMAIL       = 'email';
    public const TRAIT_EMPLOYEES   = 'employees';
    public const TRAIT_ID          = 'id';
    public const TRAIT_INDUSTRY    = 'industry';
    public const TRAIT_NAME        = 'name';
    public const TRAIT_PHONE       = 'phone';
    public const TRAIT_WEBSITE     = 'website';
    public const TRAIT_PLAN        = 'plan';

    use AnonymousAwareTrait, TimestampAwareTrait {
        AnonymousAwareTrait::jsonSerialize as serializeAnonymous;
        TimestampAwareTrait::jsonSerialize as serializeTimestamp;
    }

    private string $groupId;

    private array $traits = [];

    public function __construct(string $groupId)
    {
        parent::__construct();

        $this->groupId = $groupId;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * Free-form dictionary of traits of the group, like email or name.
     * See the Traits field docs for a list of reserved trait names.
     *
     * @see https://segment.com/docs/connections/spec/group#traits
     */
    public function withTrait(string $trait, $value): self
    {
        $this->traits[$trait] = $value;

        return $this;
    }

    /**
     * Street address of a group
     *
     * This should be a dictionary containing optional city, country, postalCode, state or street.
     */
    public function withAddressTrait(array $address): self
    {
        return $this->withTrait(self::TRAIT_ADDRESS, $address);
    }

    /**
     * URL to an avatar image for the group
     */
    public function withAvatarTrait(string $avatar): self
    {
        return $this->withTrait(self::TRAIT_AVATAR, $avatar);
    }

    /**
     * Date the group’s account was first created We recommend ISO-8601 date strings.
     */
    public function withCreatedAtTrait(\DateTimeInterface $createdAt): self
    {
        return $this->withTrait(self::TRAIT_CREATED_AT, $createdAt->format(\DateTimeInterface::ATOM));
    }

    /**
     * Description of the group, like their personal bio
     */
    public function withDescriptionTrait(string $description): self
    {
        return $this->withTrait(self::TRAIT_DESCRIPTION, $description);
    }

    /**
     * Email address of group
     */
    public function withEmailTrait(string $email): self
    {
        return $this->withTrait(self::TRAIT_EMAIL, $email);
    }

    /**
     * Number of employees of a group, typically used for companies
     */
    public function withEmployeesTrait(int $employees): self
    {
        return $this->withTrait(self::TRAIT_EMPLOYEES, $employees);
    }

    /**
     * Unique ID in your database for a group
     */
    public function withIdTrait(string $id): self
    {
        return $this->withTrait(self::TRAIT_ID, $id);
    }

    /**
     * Industry a user works in, or a group is part of
     */
    public function withIndustryTrait(string $industry): self
    {
        return $this->withTrait(self::TRAIT_INDUSTRY, $industry);
    }

    /**
     * Name of a group
     */
    public function withNameTrait(string $name): self
    {
        return $this->withTrait(self::TRAIT_NAME, $name);
    }

    /**
     * Phone number of a group
     */
    public function withPhoneTrait(string $phone): self
    {
        return $this->withTrait(self::TRAIT_PHONE, $phone);
    }

    /**
     * Website of a group
     */
    public function withWebsiteTrait(string $website): self
    {
        return $this->withTrait(self::TRAIT_WEBSITE, $website);
    }

    /**
     * Plan that a group is in
     */
    public function withPlanTrait(string $plan): self
    {
        return $this->withTrait(self::TRAIT_PLAN, $plan);
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->serializeAnonymous(),
            $this->serializeTimestamp(),
            parent::jsonSerialize(),
            array_filter([
                'groupId' => $this->groupId,
                'traits'  => $this->traits,
            ])
        );
    }
}
