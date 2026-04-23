<?php

namespace Dice;

require_once __DIR__ . '/Exceptions.php';

use Dice\Exception\DiceAuthException;
use Dice\Exception\DiceConnectionException;
use Dice\Exception\DiceException;
use Dice\Exception\DiceNewIPException;
use Dice\Exception\DiceTemplateException;
use Dice\Exception\DiceTokenExpiredException;
use Dice\Exception\DiceValidationException;
use GuzzleHttp\Exception\GuzzleException;

class BaseClient
{
    private $auth;
    private $base_url;
    private $message_url;

    public function __construct($auth, $base_url)
    {
        $this->auth = $auth;
        $this->base_url = $base_url;
        $this->message_url = $base_url . '/send-message/v1';
    }

    public function post($payload)
    {
        $token = $this->auth->getToken();
        if (!$token) {
            throw new DiceAuthException('Could not authenticate with DICE');
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'User-Agent' => 'Php Requests',
            'Content-Type' => 'application/json',
            'X-Dice-SDK-Version' => '1.0.0',
            'X-Dice-SDK-Runtime' => 'PHP ' . phpversion(),
            'X-Dice-SDK-Platform' => PHP_OS
        ];

        $templateId = $payload['template_id'] ?? null;
        if (!$templateId) {
            throw new DiceValidationException('template_id is required');
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($this->message_url, [
                'json' => $payload,
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => 10
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = null;
            }

            if ($statusCode == 401) {
                throw new DiceTokenExpiredException('Token expired');
            }

            if ($statusCode == 403) {
                throw new DiceNewIPException('Request blocked - new IP detected. Check your email to approve.');
            }

            if ($statusCode == 404) {
                throw new DiceTemplateException("Template {$templateId} not found in DICE");
            }

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'response_status' => $statusCode,
                'data' => $data
            ];
        } catch (DiceException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            throw new DiceConnectionException('Could not reach DICE server');
        }
    }
}
