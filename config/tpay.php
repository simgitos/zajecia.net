<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tpay Configuration
    |--------------------------------------------------------------------------
    |
    | Konfiguracja integracji z bramką płatności Tpay OpenAPI.
    |
    */

    'client_id' => env('TPAY_CLIENT_ID', ''),
    'client_secret' => env('TPAY_CLIENT_SECRET', ''),
    'merchant_secret' => env('TPAY_MERCHANT_SECRET', ''),
    'production_mode' => (bool) env('TPAY_PRODUCTION_MODE', false),

];
