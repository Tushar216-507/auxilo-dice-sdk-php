<?php

namespace Dice;

use GuzzleHttp\Exception\GuzzleException;

class AuthManager
{
    private $username;
    private $password;
    private $base_url;
    private $auth_url;
    private $_token = null;
    private $_expires_at = null;

    public function __construct($username, $password, $base_url)
    {
        $this->username = $username;
        $this->password = $password;
        $this->base_url = $base_url;
        $this->auth_url = $base_url . '/token';
        $this->_token = null;
        $this->_expires_at = null;
    }

    private function fetchNewToken()
    {
        $credentials = "{$this->username}:{$this->password}";
        $auth_header = base64_encode($credentials);

        $headers = [
            'Authorization' => 'Basic ' . $auth_header,
            'User-Agent' => 'Php Requests'
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($this->auth_url, [
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => 10
            ]);

            if ($response === null || $response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return null;
            }

            $result = json_decode($response->getBody()->getContents(), true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
                return null;
            }

            $token = $result['access_token']['data']['access_token'] ?? null;

            if ($token !== null) {
                $this->_token = $token;
                $this->_expires_at = time() + (6 * 24 * 60 * 60);

                return $token;
            }

            return null;
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function getToken()
    {
        if ($this->_token !== null && $this->_expires_at !== null && $this->_expires_at > time()) {
            return $this->_token;
        }

        return $this->fetchNewToken();
    }
}
