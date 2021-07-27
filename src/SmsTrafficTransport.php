<?php

namespace Prokl\Component\Notifier\Bridge\SmsTraffic;

use Exception;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface as HttpTransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class SmsTrafficTransport
 * @package Prokl\Component\Notifier\Bridge\SmsTraffic
 */
class SmsTrafficTransport extends AbstractTransport
{
    protected const HOST = 'api.smstraffic.ru';

    /**
     * @var string $login Логин.
     */
    private $login;

    /**
     * @var string $password Пароль.
     */
    private $password;

    /**
     * @var string $from Поле "от кого".
     */
    private $from;

    /**
     * @inheritdoc
     */
    public function __construct(
        string $username,
        string $password,
        string $from,
        HttpClientInterface $client = null,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->login = $username;
        $this->password = $password;
        $this->from = $from;

        parent::__construct($client, $dispatcher);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return sprintf('smstraffic://%s?from=%s', $this->getEndpoint(), (string)$this->from);
    }

    /**
     * @inheritdoc
     */
    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    /**
     * @inheritdoc
     */
    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        $body = [
            'login' => $this->login,
            'password' => $this->password,
            'originator' => $this->from,
            'rus' => 5,
            'message' => $message->getSubject(),
            'phones' => $message->getPhone(),
        ];

        $endpoint = sprintf('https://%s//multi.php', $this->getEndpoint());
        $response = $this->client->request('POST', $endpoint, ['body' => $body]);

        try {
            $result = $this->parseSendingResult($response->getContent(), $response);
        } catch (HttpTransportExceptionInterface $e) {
            throw new TransportException(
                'Error in smstraffic.ru server.',
                $response,
                0,
                $e
            );
        }

        if ((int)$result['code'] !== 0 && $result['result'] === 'ERROR') {
            throw new TransportException(sprintf(
                'Unable to send the SMS: code = %d, message = "%s".',
                $result['code'],
                $result['description']
            ), $response);
        }

        $sentMessage = new SentMessage($message, (string)$this);

        $sentMessage->setMessageId((string)($result['description'] ?? ''));

        return $sentMessage;
    }

    /**
     * Парсинг ответа шлюза.
     *
     * @param string            $result   Ответ шлюза.
     * @param ResponseInterface $response Response.
     *
     * @return array
     * @throws Exception
     */
    private function parseSendingResult(string $result, ResponseInterface $response)
    {
        $xml = simplexml_load_string($result);
        $result = json_decode(json_encode($xml), true);
        $requiredFields = ['result', 'code'];

        foreach ($requiredFields as $field) {
            if (!isset($result[$field])) {
                throw new TransportException(
                    "Incorrect answer. Key '".$field."' does not exist.",
                    $response,
                    0
                );
            }
        }

        return $result;
    }
}
