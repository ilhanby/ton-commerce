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

include_once 'config.php';

$product_detail_banner = [
    'Free Shipping',
    'Quality Guaranteed',
    'Free Worldwide Shipping',
    'Free Returns is Available',
];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, viewport-fit=cover"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0088CC">
    <meta name="description" content="<?php echo SITE_DESCRIPTION ?>">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo SITE_DESCRIPTION ?>">
    <meta property="og:description" content="<?php echo SITE_ABOUT ?>">
    <meta property="og:url" content="<?php echo SITE_URL ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME ?>">
    <meta property="og:image" content="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg">
    <meta property="msapplication-TileImage" content="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg">
    <title><?php echo SITE_DESCRIPTION ?></title>
    <link rel="icon" href="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg">
    <link rel="apple-touch-icon" sizes="32x32" href="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg">
    <link href="https://fonts.cdnfonts.com/css/roboto-slab-2" rel="stylesheet">
    <link href="<?php echo SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL ?>/assets/css/all.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL ?>/assets/css/style.css?v=<?php echo VERSION ?>" rel="stylesheet">
    <script src="<?php echo SITE_URL ?>/assets/js/qrcode.js"></script>
    <script src="<?php echo SITE_URL ?>/assets/js/telegram-web-app.js"></script>
    <script src="<?php echo SITE_URL ?>/assets/js/tonconnect-sdk.min.js" defer></script>
    <script src="<?php echo SITE_URL ?>/assets/js/tonweb.js" defer></script>

    <style>
        #loader {
            background: url('<?php echo SITE_URL ?>/assets/images/loading.gif') center center no-repeat rgba(0, 0, 0, .75);
        }
    </style>
</head>
<body>
<div class="website-wrapper">
    <div class="website-topbar">
        <div class="website-logo">
            <img src="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg" alt="<?php echo SITE_NAME ?>">
            <span class="text-white"><?php echo SITE_NAME ?></span>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 banner-text">
                <h4 class="banner-text-item my-3">50% OFF on All Perfumes</h4>
            </div>
        </div>
    </div>

    <div class="container">
        <span id="loader_text" class="d-none"></span>
        <div id="loader" style="display: block"></div>
        <div id="product_list" class="row"></div>
        <div class="row">
            <div class="col-12 text-center">
                <button type="button" class="btn load-more my-3" id="load_more">Load more</button>
            </div>
        </div>
        <input type="hidden" value="0" id="offset">
        <input type="hidden" value="<?php echo SITE_CURRENCY ?>" id="currency">
    </div>
</div>

<div class="modal fade" id="popup_product" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header website-topbar px-3 py-2">
                <div class="website-logo">
                    <img src="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg" alt="TON-COMMERCE">
                    <span class="mx-2 text-white"><?php echo SITE_NAME ?></span>
                </div>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="product_details"></div>
                <img src="<?php echo SITE_URL ?>/assets/images/bank-logos.jpeg" alt="Bank logos"
                     class="w-100 mb-3">
            </div>
            <div class="modal-footer d-block d-none">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_cart" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header website-topbar px-3 py-2">
                <div class="website-logo">
                    <img src="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg" alt="<?php echo SITE_NAME ?>">
                    <span class="mx-2 text-white"><?php echo SITE_NAME ?></span>
                </div>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="w-100" id="cart_list"></div>
            </div>
            <div class="modal-footer d-block d-none">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_payment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header website-topbar px-3 py-2"></div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row text-center justify-content-center" id="payment_list"></div>
                    <div class="row text-center justify-content-center" id="ton_qrcode"></div>
                </div>
            </div>
            <div class="modal-footer d-block d-none">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="popup_ton_info" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
     style="z-index:9999999;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header website-topbar px-3 py-2">
                <div class="website-logo">
                    <img src="<?php echo SITE_URL ?>/assets/images/ton-commerce.svg" alt="<?php echo SITE_NAME ?>">
                    <span class="mx-2 text-white"><?php echo SITE_NAME ?></span>
                </div>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="vipModalClose()"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row" id="ton_info">
                        <form enctype="multipart/form-data" id="ton_form" class="was-validated">
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_name">Name</label>
                                    <input type="text" class="form-control" name="ton_name"
                                           placeholder="Enter your name"
                                           id="ton_name" required>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_email">E-mail</label>
                                    <input type="email" class="form-control" name="ton_email"
                                           placeholder="Enter your e-mail" id="ton_email" required>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_phone">Phone</label>
                                    <input type="text" class="form-control" name="ton_phone"
                                           placeholder="Enter your phone"
                                           id="ton_phone" required>
                                </div>
                            </div>
                            <h5 class="text-center mt-4">Shipping Address</h5>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_country">Country</label>
                                    <input type="text" class="form-control" name="ton_country"
                                           placeholder="Enter your country" id="ton_country" required>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_state">State</label>
                                    <input type="text" class="form-control" name="ton_state"
                                           placeholder="Enter your state"
                                           id="ton_state" required>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_zip">Zip</label>
                                    <input type="text" class="form-control" name="ton_zip" placeholder="Enter your zip"
                                           id="ton_zip" required>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-group">
                                    <label for="ton_address">Address</label>
                                    <textarea class="form-control" name="ton_address" placeholder="Enter your address"
                                              rows="3" id="ton_address" required></textarea>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <button type="button" class="btn btn-primary btn-block btn-lg w-100 mt-3"
                                        id="ton_send_transaction">COMPLETE PAYMENT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer d-block d-none">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="product-sample" class="d-none">
    <div class="col-6 col-sm-6 col-lg-4 col-xl-3 my-3 px-0">
        <div class="card border-none" id="product_$product_item_id">
            $product_discount_html
            <a href="javascript:void(0)" class="product-link" data-id="$product_id">
                <img src="$product_image" class="card-img-top border-none product-image" alt="$product_image_alt">
            </a>

            <div class="card-body text-center justify-content-center pt-1">
                <h6 class="product-title">$product_name</h6>
                <div class="product-price">$product_price_html</div>
                <button type="button" class="btn btn-dark mt-3 add-to-cart"
                        data-id="$product_id" data-price="$product_price">ADD TO CART
                </button>
                <div class="container-fluid product-detail-banner d-none">
                    <hr class="my-4"/>
                    <div class="row">
                        <?php foreach ($product_detail_banner as $key => $item): ?>
                            <div class="col-12 col-sm-6 mt-2">
                                <img src="<?php echo SITE_URL ?>/assets/images/tick.svg" alt="Tick"
                                     style="margin-right: 10px">
                                <span><?php echo $item ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="product-description d-none">
                    <hr class="my-4"/>
                    <p>$product_description</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="<?php echo SITE_URL ?>/assets/js/jquery.min.js"></script>
<script src="<?php echo SITE_URL ?>/assets/js/popper.min.js"></script>
<script src="<?php echo SITE_URL ?>/assets/js/bootstrap.min.js"></script>

<script>
    const $site_url = '<?php echo SITE_URL ?>';
    const $ton_price_api_url = '<?php echo TON_PRICE_API_URL ?>';
    const $ton_api_manifest_url = '<?php echo TON_API_MANIFEST_URL ?>';
</script>

<script src="<?php echo SITE_URL ?>/assets/js/app.js?v=<?php echo VERSION ?>"></script>
</body>
</html>