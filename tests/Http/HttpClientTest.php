<?php declare(strict_types=1);

namespace Weirdly\Segment\Tests\Http;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\HttpClient\Psr18Client;
use Weirdly\Segment\Http\HttpClient;

class HttpClientTest extends TestCase
{
    use ProphecyTrait;

    private const URL       = 'https://example.local/some/uri';
    private const WRITE_KEY = 'foobar';

    /**
     * @var ObjectProphecy|ClientInterface
     */
    private ObjectProphecy $client;

    private \JsonSerializable $body;

    protected function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);

        $this->body = new class () implements \JsonSerializable {
            public array $body = [];

            public function jsonSerialize(): array
            {
                return $this->body;
            }
        };
    }

    public function testExpectedRequestPosted(): void
    {
        $this->body->body = [
            'foo' => 'bar',
        ];

        $psr18Client = new Psr18Client();

        $this->client->sendRequest(Argument::that(\Closure::fromCallable([$this, 'isRequestExpected'])))
            ->shouldBeCalled()
            ->willReturn(new Response());

        $httpClient = new HttpClient(self::WRITE_KEY, $this->client->reveal(), $psr18Client, $psr18Client);

        $httpClient->post(self::URL, $this->body);
    }

    public function isRequestExpected(RequestInterface $request): bool
    {
        self::assertTrue($request->hasHeader('Authorization'), 'Authorization header not set');
        self::assertSame(sprintf('Basic %s', base64_encode(sprintf('%s:', self::WRITE_KEY))), $request->getHeaderLine('Authorization'), 'Unexpected Authorization header value');

        self::assertTrue($request->hasHeader('Content-Type'), 'Content-Type header not set');
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'), 'Unexpected Content-Type header value');

        self::assertSame('{"foo":"bar"}', $request->getBody()->getContents(), 'Unexpected body content');

        return true;
    }
}
