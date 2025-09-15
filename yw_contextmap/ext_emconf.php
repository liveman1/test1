<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'YW Context Map',
    'description' => 'Frontend plugin to render yummy.world context map with OpenSeadragon overlays & menus.',
    'category' => 'plugin',
    'author' => 'yummy.world',
    'author_email' => 'info@yummy.world',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.9.99',
            'fluid' => '11.5.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
