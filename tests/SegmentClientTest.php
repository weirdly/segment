<?php declare(strict_types=1);

namespace Weirdly\Segment\Tests;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Weirdly\Segment\Http\HttpClient;
use Weirdly\Segment\Request\Alias;
use Weirdly\Segment\Request\Group;
use Weirdly\Segment\Request\Identify;
use Weirdly\Segment\Request\Page;
use Weirdly\Segment\Request\Screen;
use Weirdly\Segment\Request\Track;
use Weirdly\Segment\SegmentClient;

class SegmentClientTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var HttpClient|ObjectProphecy
     */
    private ObjectProphecy $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClient::class);
    }

    /**
     * @dataProvider getDataForIdentifyCall
     */
    public function testIdentifyCall(string $expectedUserId, array $expectedTraits, bool $expectedAnonymity, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/identify', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedTraits, $expectedAnonymity): bool {
            self::assertInstanceOf(Identify::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $expectedAnonymity ? $request->getAnonymousId() : $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedTraits, $request->getTraits(), 'Unexpected traits');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->identify($expectedUserId, $expectedTraits, $expectedAnonymity);
    }

    public function getDataForIdentifyCall(): array
    {
        return [
            ['userid', ['my' => 'trait'], true, 'v1'],
            ['userid', ['my' => 'trait'], false, 'v1'],
        ];
    }

    /**
     * @dataProvider getDataForTrackCall
     */
    public function testTrackCall(string $expectedUserId, string $expectedEvent, array $expectedProperties, bool $expectedAnonymity, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/track', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedEvent, $expectedProperties, $expectedAnonymity): bool {
            self::assertInstanceOf(Track::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $expectedAnonymity ? $request->getAnonymousId() : $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedEvent, $request->getEvent(), 'Unexpected event');
            self::assertSame($expectedProperties, $request->getProperties(), 'Unexpected properties');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->track($expectedUserId, $expectedEvent, $expectedProperties, $expectedAnonymity);
    }

    public function getDataForTrackCall(): array
    {
        return [
            ['userid', 'an event', ['my' => 'property'], true, 'v1'],
            ['userid', 'an event', ['my' => 'property'], false, 'v1'],
        ];
    }

    /**
     * @dataProvider getDataForPageCall
     */
    public function testPageCall(string $expectedUserId, string $expectedName, bool $expectedAnonymity, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/page', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedName, $expectedAnonymity): bool {
            self::assertInstanceOf(Page::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $expectedAnonymity ? $request->getAnonymousId() : $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedName, $request->getName(), 'Unexpected name');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->page($expectedUserId, $expectedName, $expectedAnonymity);
    }

    public function getDataForPageCall(): array
    {
        return [
            ['userid', 'A Page', true, 'v1'],
            ['userid', 'Another page', false, 'v1'],
        ];
    }

    /**
     * @dataProvider getDataForPageCall
     */
    public function testScreenCall(string $expectedUserId, string $expectedName, bool $expectedAnonymity, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/screen', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedName, $expectedAnonymity): bool {
            self::assertInstanceOf(Screen::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $expectedAnonymity ? $request->getAnonymousId() : $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedName, $request->getName(), 'Unexpected name');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->screen($expectedUserId, $expectedName, $expectedAnonymity);
    }

    public function getDataForScreenCall(): array
    {
        return [
            ['userid', 'A Screen', true, 'v1'],
            ['userid', 'Another screen', false, 'v1'],
        ];
    }

    /**
     * @dataProvider getDataForGroupCall
     */
    public function testGroupCall(string $expectedUserId, string $expectedGroupId, array $expectedTraits, bool $expectedAnonymity, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/group', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedGroupId, $expectedTraits, $expectedAnonymity): bool {
            self::assertInstanceOf(Group::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $expectedAnonymity ? $request->getAnonymousId() : $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedGroupId, $request->getGroupId(), 'Unexpected group id');
            self::assertSame($expectedTraits, $request->getTraits(), 'Unexpected traits');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->group($expectedUserId, $expectedGroupId, $expectedTraits, $expectedAnonymity);
    }

    public function getDataForGroupCall(): array
    {
        return [
            ['userid', 'A Group', ['some' => 'trait'], true, 'v1'],
            ['userid', 'Another group', ['some' => 'trait'], false, 'v1'],
        ];
    }

    /**
     * @dataProvider getDataForAliasCall
     */
    public function testAliasCall(string $expectedUserId, string $expectedPreviousId, string $expectedVersion): void
    {
        $urlChecks = Argument::that(function (string $url) use ($expectedVersion) {
            self::assertSame(sprintf('https://api.segment.io/%s/alias', $expectedVersion), $url, 'Unexpected URL');

            return true;
        });

        $requestChecks = Argument::that(static function ($request) use ($expectedUserId, $expectedPreviousId): bool {
            self::assertInstanceOf(Alias::class, $request, 'Unexpected request type');
            self::assertSame($expectedUserId, $request->getUserId(), 'Unexpected user id');
            self::assertSame($expectedPreviousId, $request->getPreviousId(), 'Unexpected previous id');

            return true;
        });

        $this->httpClient->post($urlChecks, $requestChecks)->shouldBeCalled()->willReturn(new Response());

        $segmentClient = new SegmentClient($this->httpClient->reveal());
        $segmentClient->alias($expectedUserId, $expectedPreviousId);
    }

    public function getDataForAliasCall(): array
    {
        return [
            ['userid', 'anewid', 'v1'],
            ['userid', 'Anotherid', 'v1'],
        ];
    }
}
