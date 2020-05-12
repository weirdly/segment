<?php declare(strict_types=1);

namespace Weirdly\Segment\Request;

/**
 * @see https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#identify
 */
final class Identify extends AbstractRequest
{
    public const TRAIT_ADDRESS     = 'address';
    public const TRAIT_AGE         = 'age';
    public const TRAIT_AVATAR      = 'avatar';
    public const TRAIT_BIRTHDAY    = 'birthday';
    public const TRAIT_COMPANY     = 'company';
    public const TRAIT_CREATED_AT  = 'createdAt';
    public const TRAIT_DESCRIPTION = 'description';
    public const TRAIT_EMAIL       = 'email';
    public const TRAIT_FIRST_NAME  = 'firstName';
    public const TRAIT_GENDER      = 'gender';
    public const TRAIT_ID          = 'id';
    public const TRAIT_LAST_NAME   = 'lastName';
    public const TRAIT_NAME        = 'name';
    public const TRAIT_PHONE       = 'phone';
    public const TRAIT_TITLE       = 'title';
    public const TRAIT_USERNAME    = 'username';
    public const TRAIT_WEBSITE     = 'website';

    use AnonymousAwareTrait {
        jsonSerialize as serializeAnonymous;
    }

    use TimestampAwareTrait {
        jsonSerialize as serializeTimestamp;
    }

    private array $traits = [];

    /**
     * Free-form dictionary of traits of the user, like email or name.
     * See the Traits field docs for a list of reserved trait names.
     *
     * @see https://segment.com/docs/connections/spec/identify#traits
     */
    public function withTrait(string $trait, $value): self
    {
        $this->traits[$trait] = $value;

        return $this;
    }

    /**
     * Street address of a user optionally containing: city, country, postalCode, state or street
     */
    public function withAddressTrait(array $address): self
    {
        return $this->withTrait(self::TRAIT_ADDRESS, $address);
    }

    /**
     * Age of a user
     */
    public function withAgeTrait(int $age): self
    {
        return $this->withTrait(self::TRAIT_AGE, $age);
    }

    /**
     * URL to an avatar image for the user
     */
    public function withAvatarTrait(string $avatar): self
    {
        return $this->withTrait(self::TRAIT_AVATAR, $avatar);
    }

    /**
     * User’s birthday
     */
    public function withBirthdayTrait(\DateTimeInterface $birthday): self
    {
        return $this->withTrait(self::TRAIT_BIRTHDAY, $birthday->format('Y-m-d'));
    }

    /**
     * Company the user represents, optionally containing: name (a String), id (a String or Number), industry (a String), employee_count (a Number) or plan (a String)
     */
    public function withCompanyTrait(array $company): self
    {
        return $this->withTrait(self::TRAIT_COMPANY, $company);
    }

    /**
     * Date the user’s account was first created. We recommend ISO-8601 date strings.
     */
    public function withCreatedAtTrait(\DateTimeInterface $createdAt): self
    {
        return $this->withTrait(self::TRAIT_CREATED_AT, $createdAt->format(\DateTimeInterface::ATOM));
    }

    /**
     * Description of the user
     */
    public function withDescriptionTrait(string $description): self
    {
        return $this->withTrait(self::TRAIT_DESCRIPTION, $description);
    }

    /**
     * Email address of a user
     */
    public function withEmailTrait(string $email): self
    {
        return $this->withTrait(self::TRAIT_EMAIL, $email);
    }

    /**
     * First name of a user
     */
    public function withFirstNameTrait(string $firstName): self
    {
        return $this->withTrait(self::TRAIT_FIRST_NAME, $firstName);
    }

    /**
     * Gender of a user
     */
    public function withGenderTrait(string $gender): self
    {
        return $this->withTrait(self::TRAIT_GENDER, $gender);
    }

    /**
     * Unique ID in your database for a user
     */
    public function withIdTrait(string $id): self
    {
        return $this->withTrait(self::TRAIT_ID, $id);
    }

    /**
     * Last name of a user
     */
    public function withLastNameTrait(string $lastName): self
    {
        return $this->withTrait(self::TRAIT_LAST_NAME, $lastName);
    }

    /**
     * Full name of a user. If you only pass a first and last name we’ll automatically fill in the full name for you.
     */
    public function withNameTrait(string $name): self
    {
        return $this->withTrait(self::TRAIT_NAME, $name);
    }

    /**
     * Phone number of a user
     */
    public function withPhoneTrait(string $phone): self
    {
        return $this->withTrait(self::TRAIT_PHONE, $phone);
    }

    /**
     * Title of a user, usually related to their position at a specific company. Example: “VP of Engineering”
     */
    public function withTitleTrait(string $title): self
    {
        return $this->withTrait(self::TRAIT_TITLE, $title);
    }

    /**
     * User’s username. This should be unique to each user, like the usernames of Twitter or GitHub.
     */
    public function withUsernameTrait(string $username): self
    {
        return $this->withTrait(self::TRAIT_USERNAME, $username);
    }

    /**
     * Website of a user
     */
    public function withWebsiteTrait(string $website): self
    {
        return $this->withTrait(self::TRAIT_WEBSITE, $website);
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
                'traits' => $this->traits,
            ])
        );
    }
}
