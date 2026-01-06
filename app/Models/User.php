<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * Database connection for this model
     * Connection: izzhilmy (PostgreSQL)
     * Tables: users, roles, permissions, password_reset_tokens, sessions
     */
    protected $connection = 'izzhilmy';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Cross-Database Relationships
    // ⚠️ These relationships span different database connections

    /**
     * Get the donor profile (hannah database - MySQL)
     */
    public function donor()
    {
        return $this->setConnection('hannah')
            ->hasOne(Donor::class, 'User_ID');
    }

    /**
     * Get the public profile (adam database - MySQL)
     */
    public function publicProfile()
    {
        return $this->setConnection('adam')
            ->hasOne(PublicProfile::class, 'User_ID');
    }

    /**
     * Get the organization profile (izzati database - PostgreSQL)
     */
    public function organization()
    {
        return $this->setConnection('izzati')
            ->hasOne(Organization::class, 'Organizer_ID');
    }

    /**
     * Get the volunteer profile (sashvini database - MariaDB)
     */
    public function volunteer()
    {
        return $this->setConnection('sashvini')
            ->hasOne(Volunteer::class, 'User_ID');
    }
}
