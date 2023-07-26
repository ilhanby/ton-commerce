<?php
// NO CHANGES
defined('TON_PRICE_API_URL') or define('TON_PRICE_API_URL', 'https://tonapi.io/v2/rates?tokens=ton&currencies=USD');

// REQUIRED
defined('VERSION') or define('VERSION', '1.0.0');
defined('TELEGRAM_BOT_TOKEN') or define('TELEGRAM_BOT_TOKEN', '');
defined('TELEGRAM_BOT_ONLY_TRUSTED') or define('TELEGRAM_BOT_ONLY_TRUSTED', false);
defined('TELEGRAM_BOT_TRUSTED') or define('TELEGRAM_BOT_TRUSTED', []);
defined('TON_API_MANIFEST_URL') or define('TON_API_MANIFEST_URL', '');
defined('TON_API_ADDRESS') or define('TON_API_ADDRESS', '');
defined('TON_API_PUBLIC_ADDRESS') or define('TON_API_PUBLIC_ADDRESS', '');
defined('SITE_URL') or define('SITE_URL', '');
defined('SITE_CURRENCY') or define('SITE_CURRENCY', 'USD');

// OPTIONAL
defined('SITE_NAME') or define('SITE_NAME', 'TON - COMMERCE');
defined('SITE_DESCRIPTION') or define('SITE_DESCRIPTION', 'Ton-Commerce - Designer Perfumes for Men & Women');
defined('SITE_ABOUT') or define('SITE_ABOUT', 'Make the most of the sunshine by shopping the spectacular selection of men’s designer perfumes at Ton-Commerce');
