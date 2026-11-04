<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend Refresh
    |--------------------------------------------------------------------------
    |
    | After a post is saved or deleted, the portfolio website is asked to
    | refresh its blog straight away. Leave the URL empty to skip this; the
    | website still refreshes on its own every few minutes.
    |
    */

    'frontend_url' => env('FRONTEND_URL'),

    'revalidate_secret' => env('FRONTEND_REVALIDATE_SECRET'),

];
