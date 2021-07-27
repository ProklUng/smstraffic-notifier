<?php

namespace Prokl\Component\Notifier\Bridge\SmsTraffic;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

/**
 * Class SmsTrafficTransportFactory
 * @package Prokl\Component\Notifier\Bridge\SmsTraffic
 */
class SmsTrafficTransportFactory extends AbstractTransportFactory
{
    /**
     * @inheritdoc
     */
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        if ('smstraffic' !== $scheme) {
            throw new UnsupportedSchemeException($dsn, 'smstraffic', $this->getSupportedSchemes());
        }

        $login = $dsn->getUser();
        $password = $dsn->getPassword();
        $from = $dsn->getRequiredOption('from');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();

        return (new SmsTrafficTransport($login, $password, $from, $this->client, $this->dispatcher))->setHost($host);
    }

    /**
     * @inheritdoc
     */
    protected function getSupportedSchemes(): array
    {
        return ['smstraffic'];
    }
}