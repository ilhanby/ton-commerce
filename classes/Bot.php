<?php
/*
 * Ton-Commerce | Telegram Web App
 * Description: Telegram Web App RestAPI v3 Integration for Ton-Commerce
 *
 * Telegram WebApp: https://core.telegram.org/bots/webapps
 *
 * Telegram Bot API: https://core.telegram.org/bots/api
 * Telegram Bot API - Updates: https://api.telegram.org/bot<token>/getUpdates
 * Telegram Bot API - Webhook set: https://api.telegram.org/bot<token>/setwebhook?url=<url>
 * Telegram Bot API - Webhook delete: https://api.telegram.org/bot<token>/deletewebhook
 *
 *
 * Author: VIPBrands | İlhanbey Arıar
 * Version: 1.0.0
 *
 */

include_once '../config.php';
class Bot
{
    private string $api_url;
    private bool $only_trusted;
    private array $trusted;
    private string $chat_id;
    private array $inline_keyboard;

    public function __construct($conf, $chat_id)
    {
        $this->api_url = 'https://api.telegram.org/bot' . $conf['bot_token'];
        $this->only_trusted = $conf['only_trusted'] ?? true;
        $this->trusted = $conf['trusted'] ?? array();
        $this->chat_id = $chat_id ?? "";
        $this->inline_keyboard = array('inline_keyboard' => array(array(array('text' => 'Shop Now', 'web_app' => array('url' => SITE_URL))))) ?? array();
    }

    public function help(): bool
    {
        $message = "<b>General Help</b>" . chr(10) . chr(10);
        $message .= "<b>/help server</b>" . chr(10) . "  - List the server related commands" . chr(10) . chr(10);
        $message .= "<b>/{other}</b>" . chr(10) . "  - Default response for unknown commands" . chr(10) . chr(10);

        return $this->send($message);
    }

    public function isTrusted(): bool
    {
        if (!$this->only_trusted) {
            return true;
        }

        if (in_array($this->chat_id, $this->trusted)) {
            return true;
        }

        return false;
    }

    public function send($message): bool
    {
        $text = trim($message);

        if (strlen(trim($text)) > 0) {
            $send = $this->api_url . "/sendmessage?parse_mode=html&chat_id=" . $this->chat_id . "&text=" . urlencode($text) . "&reply_markup=" . json_encode($this->inline_keyboard);
            file_get_contents($send);
            return true;
        }

        return false;
    }

    public function preCheckoutQuery($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . "/answerPreCheckoutQuery");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "ok" => true,
            "pre_checkout_query_id" => $data['pre_checkout_query_id'],
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function unauthorized(): bool
    {
        return $this->send('You are not authorized to use commands in this bot!');
    }

    public function unknown(): bool
    {
        return $this->send("Make the most of the sunshine by shopping the spectacular selection of men’s designer perfumes at Ton-Commerce");
    }
}