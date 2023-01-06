<?php

return [
    // GMO
    'GMO' => [
        'PAYMENT_STATUS' => [
            'DEFAULT' => 0,
            'COMPLETE' => 1,
            'WAITING_DEPOSIT' => 2,
            'BILLING_HOLD' => 3,
            'CANCEL' => 8,
            'ERROR' => 9,
        ],
        'PAYMENT_METHOD' => [
            'CREDIT_CARD' => 1,
            'BANK_TRANSFER' => 2,
            'BILL_PAYMENT' => 3,
        ],
    ],

    // HTTP CODE
    'HTTP_CODE' => [
        'SUCCESS' => 200,
        'UNAUTHORIZED' => 401,
        'FORBIDDEN' => 403,
        'NOT_FOUND' => 404,
        'METHOD_NOT_ALLOWED' => 405,
        'SERVER_ERROR' => 500,
    ],
];
