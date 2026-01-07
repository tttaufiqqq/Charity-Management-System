<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * The database connection that should be used by the model.
     *
     * Uses 'izzhilmy' connection as per Charity-Izz distributed architecture
     * where authentication and permissions are centralized.
     *
     * @var string
     */
    protected $connection = 'izzhilmy';
}
