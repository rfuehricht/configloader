<?php

return [
    'frontend' => [
        'rfuehricht/configloader/load-configuration' => [
            'target' => \Rfuehricht\Configloader\Middleware\LoadConfiguration::class,
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
            'target' => \Rfuehricht\Configloader\Middleware\LoadConfiguration::class,
            'after' => [
                'typo3/cms-backend/site-resolver',
            ]
        ],
    ]
];