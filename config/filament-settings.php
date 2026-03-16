<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Settings Keys
    |--------------------------------------------------------------------------
    |
    | Canonical setting keys for theme customization.
    |
    */
    'theme_keys' => [
        'theme.primary',
        'theme.primary_hover',
        'theme.topbar_link',
        'theme.topbar_link_hover_bg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Theme Values
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'theme.primary' => '#1f3f66',
        'theme.primary_hover' => '#193452',
        'theme.topbar_link' => '#1f3f66',
        'theme.topbar_link_hover_bg' => 'rgba(31, 63, 102, 0.08)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scoped Model Options
    |--------------------------------------------------------------------------
    |
    | Models available in the "setting_values" scope selector.
    | Key: fully-qualified class name, Value: label shown in UI.
    |
    */
    'settable_models' => [
        // App\Models\User => 'User',
        // App\Models\Product => 'Product',
        // App\Models\Reservation => 'Reservation',
    ],
];
