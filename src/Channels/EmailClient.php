<?php

namespace Dice\Channels;

class EmailClient{
    private $client;

    public function __construct($baseClient){
        $this->client = $baseClient;
    }

    public function send($email, $template_id, $template_attr,$subject,$email_from_name,$source='SDK',$message_type = 'transactional'){
        $payload = [
            'email' => $email,
            'channel' => 'email',
            'source' => $source,
            'type' => $message_type,    
            'template_id' => $template_id,
            'email_subject' => $subject,
            'email_from_name' => $email_from_name,
            'template_attr' => $template_attr
        ];

        return $this->client->post($payload);
    }
}