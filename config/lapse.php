<?php

return [
    'prefix' => 'admin',

    'channels' => [
        'mail' => env("ADMIN_EMAIL"),
    ],
    // Currently three notification channels supported
    // Those are database, slack and email
    'via' => ['mail']
];
