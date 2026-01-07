<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
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
