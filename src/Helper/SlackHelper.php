<?php


namespace App\Helper;


use Maknz\Slack\Client;

class SlackHelper
{
    static public function send($message)
    {
        $config = $GLOBALS['systemConfig']['slack'];

        if (!$config['sendError']) return;

        // Instantiate without defaults
        /** @var Client $client */
        // $client = new Client('https://hooks.slack.com/services/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');

        // Instantiate with defaults, so all messages created
        // will be sent from 'Cyril' and to the #accounting channel
        // by default. Any names like @regan or #channel will also be linked.
        $settings = [
            'username' => $config['username'],
            'channel' => $config['channel'],
            'link_names' => true
        ];

        $client = new Client($config['webHooks'], $settings);

        $client->send($message);
    }
}