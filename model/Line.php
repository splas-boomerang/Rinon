<?php

class Line
{
    private $channel_id;
    private $channel_secret;
    private $mid;

    public function __construct($channel_id, $channel_secret, $mid)
    {
        $this->channel_id = $channel_id;
        $this->channel_secret = $channel_secret;
        $this->mid = $mid;
    }

    public function api_send_line($send_id, $text){
        $post = <<< EOM
        {
            {$send_id},
            "toChannel":1383378250,
            "eventType":"138311608800106203",
            "content":{
                "toType":1,
                "contentType":1,
                "text":{$text}
            }
        }
EOM;
        $this->api_post_request("/v1/events", $post);
    }

    public function api_post_request($path, $post) {
        $url = "https://trialbot-api.line.me{$path}";
        $headers = array(
            "Content-Type: application/json",
            "X-Line-ChannelID: {$this->channel_id}",
            "X-Line-ChannelSecret: {$this->channel_secret}",
            "X-Line-Trusted-User-With-ACL: {$this->mid}"
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        error_log($output);
    }

    public function api_get_user_profile_request($mid) {
        $url = "https://trialbot-api.line.me/v1/profiles?mids={$mid}";
        $headers = array(
            "X-Line-ChannelID: {$this->channel_id}",
            "X-Line-ChannelSecret: {$this->channel_secret}",
            "X-Line-Trusted-User-With-ACL: {$this->mid}"
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        return $output;
    }

    public function api_get_message_content_request($message_id) {
        $url = "https://trialbot-api.line.me/v1/bot/message/{$message_id}/content";
        $headers = array(
            "X-Line-ChannelID: {$this->channel_id}",
            "X-Line-ChannelSecret: {$this->channel_secret}",
            "X-Line-Trusted-User-With-ACL: {$this->mid}"
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        file_put_contents("/tmp/{$message_id}", $output);
    }
}