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

$filters = array(
    'offset' => cleanString($_GET['offset'] ?? 0),
    'order' => cleanString($_GET['order'] ?? 'asc'),
    'wmc-currency' => SITE_CURRENCY,
    'min_price' => cleanString($_GET['min_price'] ?? 0),
    'max_price' => cleanString($_GET['max_price'] ?? 999999999),
    'status' => 'publish',
    'stock_status' => 'instock',
    'per_page' => cleanString($_GET['per_page'] ?? 20),
);

if (isset($_GET['include']) && !empty($_GET['include'])) {
    if (is_array($_GET['include'])) {
        $filters['include'] = implode(',', $_GET['include']);
    } else {
        $filters['include'] = $_GET['include'];
    }
}

$type = isset($_GET['type']) ? cleanString($_GET['type']) : null;
if (!is_null($type) && !empty($type)) {

    $products = json_decode(file_get_contents('../assets/data.json'), true);

    switch ($type) {
        case 'products':
            returnResponse($filters, $type, $products, 'success', 'Success get ' . $type . '.');
            break;

        case 'product':
            if (!isset($filters['include']) || empty($filters['include']) || !is_numeric($filters['include']))
                returnResponse($filters, $type, [], 'error', 'Parameter id is not valid.');

            $product = [];
            if (is_array($_GET['include'])) {
                foreach ($products as $item) {
                    if (in_array($item['id'], $_GET['include'])) {
                        $product[] = $item;
                    }
                }
            } else {
                foreach ($products as $item) {
                    if ($item['id'] == $_GET['include']) {
                        $product[] = $item;
                    }
                }
            }

            returnResponse($filters, $type, $product, 'success', 'Success get ' . $type . '.');
            break;

        case 'order':
            if ((!isset($_POST['_auth']) || empty($_POST['_auth'])) &&
                (!isset($_POST['unsafe']) || empty($_POST['unsafe'])) &&
                (!isset($_POST['params']) || empty($_POST['params'])) &&
                (!isset($_POST['type']) || empty($_POST['type']) || $_POST['type'] != $type)) {
                returnResponse($filters, $type, [], 'error', 'Parameters is not valid.');
            }

            $auth = [
                '_auth' => cleanString($_POST['_auth']),
                'unsafe' => cleanString($_POST['unsafe']),
            ];
            $params = json_decode($_POST['params'], true);

            $filters['wmc-currency'] = $_POST['currency'] ?? SITE_CURRENCY;

            if (isset($params['order_data']) && !empty($params['order_data']))
                $params['order_data'] = json_decode($params['order_data'], true);

            if (isset($params['total_price']) && !empty($params['total_price']) && is_numeric($params['total_price']) &&
                isset($params['currency']) && !empty($params['currency']) && in_array($params['currency'], [SITE_CURRENCY]) &&
                isset($params['order_data']) && !empty($params['order_data']) && is_array($params['order_data'])) {

                if ($params['currency'] != SITE_CURRENCY) {
                    $payment_currency_ids = array();
                    foreach ($params['order_data'] as $id => $item) {
                        $payment_currency_ids[] = $id;
                    }

                    $payment_currency_data = [
                        'include' => implode(',', $payment_currency_ids),
                        'wmc-currency' => SITE_CURRENCY,
                    ];

                    $payment_order_price = 0;
                    foreach ($products as $product) {
                        if (isset($params['order_data'][$product['id']])) {
                            $params['order_data'][$product['id']]['price'] = $product['price'];
                            $payment_order_price += $product['price'] * $params['order_data'][$product['id']]['count'];
                        }
                    }

                    $params['total_price'] = $payment_order_price;
                    $params['currency'] = SITE_CURRENCY;
                }

                $order = createOrder($auth, $params);

                if ($order['status'] === 'success') {
                    returnResponse($filters, $type, $order['data'], $order['status'], $order['message']);
                } else {
                    returnResponse($filters, $type, [], 'error', 'An error occurred while receiving the payment. Please try again later.');
                }

            } else {
                returnResponse($filters, $type, [], 'error', 'Parameters is not valid.');
            }

            break;

        case 'order_check':
            if ((!isset($_POST['_auth']) || empty($_POST['_auth'])) &&
                (!isset($_POST['unsafe']) || empty($_POST['unsafe'])) &&
                (!isset($_POST['params']) || empty($_POST['params'])) &&
                (!isset($_POST['type']) || empty($_POST['type']) || $_POST['type'] != $type) &&
                (!isset($_POST['boc']) || empty($_POST['boc']))) {
                returnResponse($filters, $type, [], 'error', 'Parameters is not valid.');
            }

            $auth = [
                '_auth' => cleanString($_POST['_auth']),
                'unsafe' => cleanString($_POST['unsafe']),
            ];
            $params = json_decode($_POST['params'], true);
            $boc = cleanString($_POST['boc']);

            $conf = [
                'bot_token' => TELEGRAM_BOT_TOKEN,
                'only_trusted' => false,
                'trusted' => []
            ];

            $hook = new Bot($conf, $auth['unsafe']['user']['id']);
            if (!$hook->isTrusted()) {
                returnResponse($filters, $type, [], 'error', 'Unauthorized request.');
            }

            /*
             *
             * DB INSERT FOR ORDER
             * .
             * .
             * .
             */

            $hook->send("Your payment has been completed successfully.\n\nDon't panic just demo :))");

            returnResponse($filters, $type, [], "success", "Your order has been successfully received. We will contact you as soon as possible.");

            break;
        default:
            break;
    }
}

returnResponse($filters, $type, [], 'error', 'Something went wrong. Please try again later.');

function createOrder($auth, $params): array
{
    $conf = [
        'bot_token' => TELEGRAM_BOT_TOKEN,
        'only_trusted' => false,
        'trusted' => []
    ];

    $hook = new Bot($conf, $auth['unsafe']['user']['id']);

    if (!$hook->isTrusted()) {
        return array(
            'status' => 'error',
            'message' => 'Unauthorized request.',
            'data' => '',
        );
    }

    return array(
        'status' => 'success',
        'message' => 'Success request and response.',
        'data' => [
            'amount' => $params['total_price'],
            'address' => TON_API_ADDRESS,
            'currency' => $params['currency'],
            'ton_price' => $params['ton_price']
        ],
    );
}

function cleanString($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = cleanString($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = trim(filter_var($data, FILTER_SANITIZE_STRING));
    }

    return $data;
}

function returnResponse($filters, $type, $items = [], $status = 'success', $message = 'Success.')
{
    header('Content-Type: application/json');
    $response = array(
        'status' => $status,
        'message' => $message,
        'type' => $type,
        'filters' => $status == 'success' ? $filters : array(),
        'items' => $items,
    );
    die(json_encode($response));
}

?>