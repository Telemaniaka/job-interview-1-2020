<?php

declare(strict_types=1);

return [
    'client' => [
        'natural' => [
            'cashIn' => [
                'commissionRate' => 0.03,
                'maxCommission' => 5,
            ],
            'cashOut' => [
                'commissionRate' => 0.3,
                'dontTaxUnderPerWeek' => 1000,
            ],
        ],
        'legal' => [
            'cashIn' => [
                'commissionRate' => 0.03,
                'maxCommission' => 5,
            ],
            'cashOut' => [
                'commissionRate' => 0.3,
                'minCommission' => 0.5,
            ],
        ],
    ],
];
