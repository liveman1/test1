<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'yw t3sbootstrap addon',
    'description' => 'Adds yw_BS Carousel content element based on BS Carousel',
    'category' => 'plugin',
    'author' => 'AI Assistant',
    'author_email' => 'example@example.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            't3sbootstrap' => '5.2.13',
            'ns_yw_contextmodal' => ''
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Yw\\YwT3sbootstrapAddon\\' => 'Classes/'
        ],

    ],
];
