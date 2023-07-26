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
include_once '../classes/Bot.php';

$php_input = file_get_contents("php://input");
$content = json_decode($php_input, true);

if (isset($content["pre_checkout_query"])) {
    $data = [
        "pre_checkout_query_id" => $content["pre_checkout_query"]["id"] ?? "",
        "payload" => $content["pre_checkout_query"]["invoice_payload"] ?? "",
        "from" => $content["pre_checkout_query"]["from"] ?? "",
        "currency" => $content["pre_checkout_query"]["currency"] ?? "",
        "total_amount" => $content["pre_checkout_query"]["total_amount"] ? $content["pre_checkout_query"]["total_amount"] / 100 : "",
        "order_info" => $content["pre_checkout_query"]["order_info"] ?? ""
    ];

    $hook = new Bot(['bot_token' => TELEGRAM_BOT_TOKEN, 'only_trusted' => TELEGRAM_BOT_ONLY_TRUSTED, 'trusted' => TELEGRAM_BOT_TRUSTED], "");
    if (!$hook->isTrusted()) {
        $hook->unauthorized();
        die();
    }

    $hook->preCheckoutQuery($data);
}

if (isset($content["message"]) && !isset($content["message"]["successful_payment"])) {
    $chat_id = $content["message"]["chat"]["id"];
    $message = $content["message"]["text"];
    $args = explode(' ', trim($message));
    $command = ltrim(array_shift($args), '/');
    $banned_commands = [
        "isTrusted",
        "send",
        "sendCreateInvoiceAPI",
        "preCheckoutQuery",
        "unauthorized",
        "unknown"
    ];

    $hook = new Bot(['bot_token' => TELEGRAM_BOT_TOKEN, 'only_trusted' => TELEGRAM_BOT_ONLY_TRUSTED, 'trusted' => TELEGRAM_BOT_TRUSTED], $chat_id);
    if (!$hook->isTrusted()) {
        $hook->unauthorized();
        die();
    }

    if (method_exists($hook, $command) && !in_array($command, $banned_commands)) {
        $hook->$command();
    } else {
        $hook->unknown();
    }
} else if (isset($content["message"]["successful_payment"])) {
    $chat_id = $content["message"]["chat"]["id"];

    $data = [
        "payload" => $content["message"]["successful_payment"]["invoice_payload"] ?? "",
        "from" => $content["message"]["from"] ?? "",
        "currency" => $content["message"]["successful_payment"]["currency"] ?? "",
        "total_amount" => $content["message"]["successful_payment"]["total_amount"] ? $content["message"]["successful_payment"]["total_amount"] / 100 : "",
        "order_info" => $content["message"]["successful_payment"]["order_info"] ?? "",
    ];

    $hook = new Bot(['bot_token' => TELEGRAM_BOT_TOKEN, 'only_trusted' => TELEGRAM_BOT_ONLY_TRUSTED, 'trusted' => TELEGRAM_BOT_TRUSTED], $chat_id);
    if (!$hook->isTrusted()) {
        $hook->unauthorized();
        die();
    }

    $hook->send("Payment is successfully completed.\nThank you for your order.\nWe will contact you as soon as possible.\nDon't panic it's just a beta version.");
}

die();