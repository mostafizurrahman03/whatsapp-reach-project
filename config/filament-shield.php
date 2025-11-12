<?php

use Spatie\Permission\Models\Role;

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Shield Roles Model
    |--------------------------------------------------------------------------
    |
    | Ei option ta define kore je kon model ke Filament Shield use korbe
    | roles manage korar jonno. By default, eta Spatie Role model.
    |
    */

    'roles_model' => Role::class,

    /*
    |--------------------------------------------------------------------------
    | Permissions Model
    |--------------------------------------------------------------------------
    |
    | Filament Shield er permission model. By default Spatie permission use kore.
    |
    */

    'permissions_model' => \Spatie\Permission\Models\Permission::class,

    /*
    |--------------------------------------------------------------------------
    | Super Admin Roles
    |--------------------------------------------------------------------------
    |
    | Ei roles jader ke automatic full access thakbe, override korte parbe na.
    | Example: ['super-admin']
    |
    */

    'super_admin_roles' => [
        'super-admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Filament Shield er dashboard widgets enable/disable korte parbe.
    |
    */

    'widgets' => [
        'show_roles' => true,
        'show_permissions' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Routes
    |--------------------------------------------------------------------------
    |
    | Filament Shield routes enable/disable korte parbe. Default true.
    |
    */

    'enable_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Guard
    |--------------------------------------------------------------------------
    |
    | Filament Shield er user authentication guard. Default: 'web'
    |
    */

    'guard' => 'web',

];
