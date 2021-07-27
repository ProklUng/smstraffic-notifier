<?php

namespace Prokl\Component\Notifier\Bridge\SmsTraffic\Tests;

use Prokl\Component\Notifier\Bridge\SmsTraffic\SmsTrafficTransportFactory;
use Symfony\Component\Notifier\Test\TransportFactoryTestCase;
use Symfony\Component\Notifier\Transport\TransportFactoryInterface;

/**
 * Class SmsTrafficTransportFactoryTest
 * @package Local\Services\SmsTraffic\Tests
 */
class SmsTrafficTransportFactoryTest extends TransportFactoryTestCase
{
    /**
     * @return SmsTrafficTransportFactory
     */
    public function createFactory(): TransportFactoryInterface
    {
        return new SmsTrafficTransportFactory();
    }

    /**
     * @inheritdoc
     */
    public function createProvider(): iterable
    {
        yield [
            'smstraffic://host.test?from=MyApp',
            'smstraffic://login:password@host.test?from=MyApp',
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsProvider(): iterable
    {
        yield [true, 'smstraffic://login:password@default?from=MyApp'];
        yield [false, 'somethingElse://login:password@default?from=MyApp'];
    }

    /**
     * @inheritdoc
     */
    public function missingRequiredOptionProvider(): iterable
    {
        yield 'missing option: from' => ['smstraffic://login:password@default'];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://login:password@default?from=MyApp'];
        yield ['somethingElse://login:password@default']; // missing "from" option
    }
}