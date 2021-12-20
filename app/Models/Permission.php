<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    //Template dari tutorial di website https://qcode.in/easy-roles-and-permissions-in-laravel-5-4/
    public static function defaultPermissions()
    {
        return [
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'add_roles',
            'edit_roles',
            'delete_roles',

            'view_products',
            'add_products',
            'edit_products',
            'delete_products',

            'view_orders',
            'add_orders',
            'edit_orders',
            'delete_orders',
        ];
    }
}
