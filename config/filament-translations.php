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
        //resource_path('views'),
        //base_path('vendor')
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
    "locales" => [
        "en" => "English",
        "fi" => "Finnish",
        "sv" => "Swedish",
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
        "en",
        "fi",
        "sv",
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
        "icon" => "heroicon-o-user-circle",
        "sort" => 10,
        "url" => 'admin/translations/change',
        "position" => "user" //[user|navigation]
    ],

    /* Automatically create a translation record if it doesn't exist yet (only creates to db, not to files) */
    "auto_create" => true,

    /* Add to all locales */
    "add_all_locales" => true,

    /* If string for targeted locale doesn't exist, create the record and translate with Google. */
    "google_translate" => true,
    "google_key" => env('GOOGLE_TRANSLATION_KEY', ''),

    /* keep in mind, that your db should be somewhat as effective as your cache engine in doing
    simple fetch_all, so you don't necessarily need the cache */
    "cache" => false,

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    |
    | use simple modal resource for the translation resource
    |
    */
    "modal" => false,

];
