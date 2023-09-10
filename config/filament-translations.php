<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | add path that will be show to the scaner to catch lanuages tags
    |
    */
    "paths" => [
        app_path(),
        resource_path('views'),
        base_path('vendor')
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    |
    | set the redirect path when change the language between selected path or next request
    |
    */
    "redirect" => "next",

    /* Automatically create a translation record if it doesn't exist yet */
    "auto_create" => true,

    /* If string for targeted locale doesn't exist, create the record and translate with Google. */
    "google_translate" => true,
    "google_key" => 'YOUR_GOOGLE_KEY',

    /*
    |--------------------------------------------------------------------------
    | Excluded paths
    |--------------------------------------------------------------------------
    |
    | Put here any folder that you want to exclude that is inside of paths
    |
    */

    "excludedPaths" => [
    ],


    /*
    |--------------------------------------------------------------------------
    | Locals
    |--------------------------------------------------------------------------
    |
    | add the locals that will be show on the languages selector
    |
    */
    "locals" => [
        "en" => "English",
        "ar" => "Arabic",
        "pt_BR" => "Português (Brasil)",
        "my" => "Burmese"
    ],

    /*
    |--------------------------------------------------------------------------
    | Show Switcher
    |--------------------------------------------------------------------------
    |
    | show switcher item on the navigation menu
    |
    */
    "show-switcher" => true,

    /*
    |--------------------------------------------------------------------------
    | Switcher
    |--------------------------------------------------------------------------
    |
    | the lanuages of the switcher navigation item must be 2
    |
    */
    "switcher" => [
        "ar",
        "en",
    ],

    /*
    |--------------------------------------------------------------------------
    | Switcher Item Option
    |--------------------------------------------------------------------------
    |
    | custome switcher menu item
    |
    */

    "languages-switcher-menu" => [
        "group" => "Translations",
        "icon" => "heroicon-o-translate",
        "sort" => 10,
        "url" => 'admin/translations/change',
        "position" => "user" //[user|navigation]
    ],

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    |
    | use simple modal resource for the translation resource
    |
    */
    "modal" => true,

];
