<?php

namespace Dice\Channels;

class SmsClient{
    private $client;

    public function __construct($baseClient){
        $this->client = $baseClient;
    }

    public function send($template_id,$template_attr,$mobile_no,$source = 'SDK',$message_type = 'transactional'){
        if (empty($mobile_no) || trim($mobile_no) == ''){
            return false;
        }

        $cleanMobile = preg_replace('/\D/', '', $mobile_no);

        if (empty($cleanMobile)){
            return false;
        }

        if (strlen($cleanMobile) == 10) {
            $formattedMobile = '91' . $cleanMobile;
        } elseif (strlen($cleanMobile) == 12 && substr($cleanMobile, 0, 2) === '91') {
            $formattedMobile = $cleanMobile;
        } else {
            $formattedMobile = '91' . substr($cleanMobile, -10);
        }

        $payload = [
            'mobile_no' => $formattedMobile,
            'channel' => 'sms',
            'source' => $source,
            'type' => $message_type,
            'template_id' => $template_id,
            'template_attr' => $template_attr
        ];

        return $this->client->post($payload);
    }
}

