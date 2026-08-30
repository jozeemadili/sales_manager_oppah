<?php
$baseUrl = env('JUBILEE_BASE_URL', 'https://jubileeapiinterface.jubileetanzania.co.tz:8443/api/v2');
return [
            'system_code' => env('JUBILEE_SYSTEM_CODE', 'NBC-AGENTS'),
            'endpoints'   => array('submit_motor' => $baseUrl.'/motor/transaction'),
            'secrets'     => array('Api_Key'      => env('JUBILEE_API_KEY', 'BAgbr2BJ89MTXFX86xQ80aALDcQrpD99BSI9hKPv2jJWER--')),
            'callbackUrl' => env('JUBILEE_CALLBACK_URL', 'https://policypro.co.tz/api/v1/jubilee/callbacks')
       ];
