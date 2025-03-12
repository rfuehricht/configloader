<?php

$EM_CONF['configloader'] = [
    'title' => 'Configuration Loader',
    'description' => 'Makes it possible to load configuration files and access values in various locations.',
    'category' => 'misc',
    'version' => '1.0.2',
    'state' => 'stable',
    'author' => 'Reinhard Führicht',
    'author_email' => 'r.fuehricht@gmail.com',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-13.99.99'
        ],
        'conflicts' => [
        ]
    ],
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1
];
