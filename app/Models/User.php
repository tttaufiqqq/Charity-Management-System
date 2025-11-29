<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles;

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

    // Relationships
    public function donor()
    {
        return $this->hasOne(Donor::class, 'User_ID');
    }

    public function publicProfile()
    {
        return $this->hasOne(PublicProfile::class, 'User_ID');
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'Organizer_ID');
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class, 'User_ID');
    }
}
