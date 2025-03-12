<?php

use Rfuehricht\Configloader\Middleware\LoadConfiguration;

return [
    'frontend' => [
        'rfuehricht/configloader/load-configuration' => [
            'target' => LoadConfiguration::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/maintenance-mode'
            ]
        ],
    ],
    'backend' => [
        'rfuehricht/configloader/load-configuration' => [
            'target' => LoadConfiguration::class,
            'after' => [
                'typo3/cms-backend/site-resolver',
            ]
        ],
    ]
];
