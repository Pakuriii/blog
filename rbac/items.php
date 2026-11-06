<?php
return [
    'author' => [
        'type' => 1,
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'author',
        ],
    ],
];
