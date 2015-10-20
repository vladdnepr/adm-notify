<?php

namespace vladdnepr\adm\notify;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Amazon Device Messaging (ADM) notifier yii2-way class
 * @link https://developer.amazon.com/appsandservices/apis/engage/device-messaging/tech-docs/06-sending-a-message
 */
class Notify
{
    public $clientId;
    public $clientSecret;

    public $urlAdm = 'https://api.amazon.com/messaging/registrations/##REG_ID##/messages';
    public $urlToken = 'https://api.amazon.com/auth/O2/token';

    public $headersAdm = [
        'Content-Type' => 'application/json',
        'X-Amzn-Type-Version' => 'com.amazon.device.messaging.ADMMessage@1.0',
        'Accept' => 'application/json',
        'X-Amzn-Accept-Type' => 'com.amazon.device.messaging.ADMSendResult@1.0',
    ];
    public $headersToken = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Charset' => 'UTF-8',
    ];

    /**
     * @var ClientInterface
     */
    public $client;

    /**
     * @var string
     */
    public $token;

    /**
     * @param string $regId
     * @param string $message
     * @param null|string $consolidationKey
     * @param null|int $expiresAfter
     * @return string New registration ID
     */
    public function sendMessage($regId, $message, $consolidationKey = null, $expiresAfter = null)
    {
        return $this->sendData($regId, ['message' => $message], $consolidationKey, $expiresAfter);
    }

    /**
     * @param string $regId
     * @param array $data
     * @param null|string $consolidationKey
     * @param null|int $expiresAfter
     * @return string New registration ID
     */
    public function sendData($regId, array $data, $consolidationKey = null, $expiresAfter = null)
    {
        $client = $this->getClient();

        try {
            $regId = json_decode($client->request(
                'POST',
                strtr($this->urlAdm, ['##REG_ID##' => $regId]),
                [
                    'headers' => array_merge(
                        $this->headersAdm,
                        ['Authorization' => 'Bearer ' . $this->getAccessToken()]
                    ),
                    'json' => array_filter([
                        'data' => $data,
                        'consolidationKey' => $consolidationKey,
                        'expiresAfter' => $expiresAfter
                    ])
                ]
            )->getBody())->registrationID;
        } catch (BadResponseException $e) {
            throw new \RuntimeException(
                'ADM send message error: ' . json_decode($e->getResponse()->getBody())->reason,
                $e->getCode()
            );
        }

        return $regId;
    }

    /**
     * @return string
     */
    protected function getAccessToken()
    {
        if (!$this->token) {
            if (!$this->clientId || !$this->clientSecret) {
                throw new \LogicException('Client ID or Client secret is empty');
            }

            $client = $this->getClient();

            try {
                $this->token = json_decode($client->request('POST', $this->urlToken, [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'scope' => 'messaging:push',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                    ],
                    'headers' => $this->headersToken
                ])->getBody())->access_token;
            } catch (BadResponseException $e) {
                throw new \RuntimeException(
                    'ADM OAuth error: ' . json_decode($e->getResponse()->getBody())->error_description,
                    $e->getCode()
                );
            }
        }

        return $this->token;
    }

    /**
     * @return ClientInterface
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        if (!$this->client instanceof ClientInterface) {
            throw new \LogicException('Client must implement GuzzleHttp\\ClientInterface\\ClientInterface');
        }

        return $this->client;
    }
}
