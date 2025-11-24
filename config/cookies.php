<?php

return [
    'defaults' => [
        [
            'name' => 'macwas_session',
            'display_name' => 'macwas_session',
            'purpose' => 'Keeps you signed in securely while you move between pages.',
            'type' => 'Necessary',
            'duration' => 'Session',
            'is_required' => true,
        ],
        [
            'name' => 'macwas_cookie_consent',
            'display_name' => 'macwas_cookie_consent',
            'purpose' => 'Stores your cookie preferences so we remember your choice.',
            'type' => 'Preference',
            'duration' => '12 months',
            'is_required' => true,
        ],
        [
            'name' => 'macwas_cookie_policy',
            'display_name' => 'macwas_cookie_policy',
            'purpose' => 'Tracks whether the cookie banner still needs to be shown.',
            'type' => 'Preference',
            'duration' => '12 months',
            'is_required' => false,
        ],
    ],
];

