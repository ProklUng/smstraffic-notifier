<?php

namespace Prokl\Component\Notifier\Bridge\SmsTraffic\Tests;

use Prokl\Component\Notifier\Bridge\SmsTraffic\SmsTrafficTransport;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Test\TransportTestCase;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SmsTrafficTransportTest
 * @package Local\Services\SmsTraffic\Tests
 */
final class SmsTrafficTransportTest extends TransportTestCase
{
    /**
     * @inheritdoc
     */
    public function createTransport(HttpClientInterface $client = null): TransportInterface
    {
        return new SmsTrafficTransport
        ('login', 'password', 'MyApp', $client ?? $this->createMock(HttpClientInterface::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function toStringProvider(): iterable
    {
        yield ['smstraffic://api.smstraffic.ru?from=MyApp', $this->createTransport()];
    }

    /**
     * @inheritdoc
     */
    public function supportedMessagesProvider(): iterable
    {
        yield [new SmsMessage('0611223344', 'Hello!')];
    }

    /**
     * @inheritdoc
     */
    public function unsupportedMessagesProvider(): iterable
    {
        yield [new ChatMessage('Hello!')];
        yield [$this->createMock(MessageInterface::class)];
    }
}