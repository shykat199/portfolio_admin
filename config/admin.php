<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Account
    |--------------------------------------------------------------------------
    |
    | The single administrator account created by the AdminUserSeeder. Set
    | these in the .env file only; they are never committed. Change the
    | password from Settings after the first login.
    |
    */

    'name' => env('ADMIN_NAME', 'Admin'),

    'email' => env('ADMIN_EMAIL'),

    'password' => env('ADMIN_PASSWORD'),

];
