<?php

return [
    'environment' => env('PAYMENT_MODE', 'sandbox'),
    'spreadSheetId' => env('SPREADSHEETID', ''),                  // google sheet id
    'sandbox_client_id' => env('SANDBOX_CLIENT_ID', ''),          // sandbox client id of business paypal app
    'sandbox_client_secret' => env('SANDBOX_CLIENT_SECRET', ''),  // sandbox client secret of business paypal app 
    'live_client_id' => env('LIVE_CLIENT_ID', ''),             // live client id of business paypal app
    'live_client_secret' => env('LIVE_CLIENT_SECRET', ''),     // live client secret of business paypal app    
];
