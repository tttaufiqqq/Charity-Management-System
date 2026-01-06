<?php

// File: app/Models/PublicProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicProfile extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: adam (MySQL)
     * Tables: public_profile, recipient
     */
    protected $connection = 'adam';

    protected $table = 'public';

    protected $primaryKey = 'Public_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone',
        'Email',
        'Position',
    ];

    // Relationships

    /**
     * Get the user account for this public profile (izzhilmy database - PostgreSQL)
     * ⚠️ Cross-database relationship
     */
    public function user()
    {
        return $this->setConnection('izzhilmy')
            ->belongsTo(User::class, 'User_ID');
    }

    /**
     * Get all recipient applications for this public profile (same database - adam)
     */
    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'Public_ID', 'Public_ID');
    }
}
