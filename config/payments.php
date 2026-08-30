<?php
return [
    'evmak'				=> array(
        'api_source'	=> env('EVMAK_API_SOURCE', 'POLICY-PRO'),
        'callback'	    => env('EVMAK_CALLBACK_URL', 'http://143.198.74.44:8000/api/v1/payments/callbacks'),
        'hash'          =>  md5(env('EVMAK_HASH_SEED', 'insurance') . '|' . date('d-m-Y')),
        'user'          => env('EVMAK_USER', 'insurance'),
        'mno_types'     => array(
                        'mpesa'     => array(
                            'code'     => 'Mpesa',
                            'endpoint' => 'https://vodaapi.evmak.com/test/',
                        ),
                        'tigopesa'     => array(
                            'code'     => 'TigoPesa',
                            'endpoint' => 'https://mno.evmak.com/tigo/test/',
                        ),
                    ),
    'minimum_payment_amount' => 1000
    ),
];
