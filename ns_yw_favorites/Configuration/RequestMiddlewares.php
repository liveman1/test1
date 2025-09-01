<?php
return [
    'frontend' => [
        'nitsan/ns-yw-favorites/new-fav-list-redirect' => [
            'target' => \NITSAN\NsYwFavorites\Middleware\NewFavListRedirectMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ]
    ]
];
