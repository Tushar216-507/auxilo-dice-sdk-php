<?php

namespace Dice;
class AuthManager{
    private $username;
    private $password;
    private $base_url;
    private $auth_url;
    private $_token = null;
    private $_expires_at = null;

    public function __construct($username, $password, $base_url){
        $this->username = $username;
        $this->password = $password;
        $this->base_url = $base_url;
        $this->auth_url = $base_url . '/token';
        $this->_token = null;
        $this->_expires_at = null;
    }

    private function fetchNewToken(){
        $credentials = "{$this->username}:{$this->password}";
        $auth_header = base64_encode($credentials);

        $headers = [
            "Authorization" => "Basic ". $auth_header,
            "User-Agent" => "Python Requests"
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($this->auth_url, [
                'headers' => $headers,
                'timeout' => 10
            ]);

            if ($response == null){
                return null;
            }

            $result = json_decode($response->getBody()->getContents(), true);
            $token = $result['access_token']['data']['access_token'] ?? null;

            if ($token !== null) {
                $this->_token = $token;
                $this->_expires_at = time() + (6 * 24 * 60 * 60);
            }
            else{
                return null;
            }

            return $token;
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            return null;
        }

  
    }

    public function getToken(){
        if ($this->_token !== null && $this->_expires_at !== null && $this->_expires_at > time()) {
            return $this->_token;
        }

        $result = $this->fetchNewToken();
        if ($result === false){
            return false;
        }

        return $result;
    }
}