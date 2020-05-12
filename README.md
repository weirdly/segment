A modern [Segment](https://segment.com/) client. Uses PSR7, PSR17, and PSR18 for transport.

### Installation

```sh
composer require weirdly/segment
```

### Getting Started

Create a `SegmentClient` instance.

You'll need to provide your own implementations of the PSR standards, or install your favourite one. Suggestions:

- [symfony/http-client](https://github.com/symfony/http-client)
- [nyholm/psr7](https://github.com/Nyholm/psr7)

```php
$writeKey = 'abc123';
$httpClient = $requestFactory = $streamFactory = new Symfony\Component\HttpClient\Psr18Client();
$segmentHttpClient = new \Weirdly\Segment\Http\HttpClient($writeKey, $httpClient, $requestFactory, $streamFactory);
$client = new \Weirdly\Segment\SegmentClient($segmentHttpClient);
```

### Usage

The main requests have shortcut methods on the client.

```php
$client->identify('user_id', ['trait' => 'value']);
$client->track('user_id', 'Some Event', ['property' => 'value']);
$client->page('user_id', 'Page Name');
$client->screen('user_id', 'Screen Name');
$client->group('user_id', 'Group Name', ['trait' => 'value']);
$client->alias('user_id', 'previous_id');
```

If you don't yet know the user id, provide a unique, anonymous ID instead. Set the `$anonymous` argument to true.

```php
$client->identify('user_id', ['trait' => 'value'], true);
```

For more fine-grained control, pass a request directly to the `$client->send()` method.
E.g. to identify a previously anonymous user with a known user id, send an `identify`
request with both the user id and anonymous id.

```php
$request = \Weirdly\Segment\Request\Identify::createIdentifiedRequest('user_id')
    ->setAnonymousId('anonymous_id');
$client->send($request);
```

Some request classes have required arguments. When using the `createAnonymousRequest()` and `createIdentifiedRequest()`
helper methods, pass required arguments after the user id.

```php
$request = \Weirdly\Segment\Request\Track::createIdentifiedRequest('user_id', 'Event Name')
```

Or just instantiate the request without the helper method.
The following results in an identical request to the above example.

```php
$request = new \Weirdly\Segment\Request\Track('Event Name');
$request->setUserId('user_id');
```

Send multiple requests in a single batch round-trip.

```php
$requests = [
    \Weirdly\Segment\Request\Page::createIdentifiedRequest('user_id')->setName('Page Name'),
    \Weirdly\Segment\Request\Track::createIdentifiedRequest('user_id', 'Event Name'),
];
$client->batch(...$requests);
```
