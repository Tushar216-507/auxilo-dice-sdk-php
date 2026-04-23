<?php

namespace Dice;

use Dice\AuthManager;
use Dice\BaseClient;
use Dice\Channels\EmailClient;
use Dice\Channels\WhatsappClient;
use Dice\Channels\SmsClient;

class DiceClient {
    private $auth;
    private $base_client;
    public $email;
    public $whatsapp;
    public $sms;
    public function __construct(array $config){
        $this->auth = new AuthManager(    
            $config['username'],
            $config['password'],
            $config['base_url']
        );
        $this->base_client = new BaseClient($this->auth, $config['base_url']);
        $this->email = new EmailClient($this->base_client);
        $this->whatsapp = new WhatsappClient($this->base_client);
        $this->sms = new SmsClient($this->base_client);
    }
}