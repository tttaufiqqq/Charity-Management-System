<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationAllocation extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: hannah (MySQL)
     * Tables: donor, donation, donation_allocation
     */
    protected $connection = 'hannah';

    protected $table = 'donation_allocation';

    // Composite primary key
    protected $primaryKey = ['Recipient_ID', 'Campaign_ID'];

    public $incrementing = false;

    protected $fillable = [
        'Recipient_ID',
        'Campaign_ID',
        'Amount_Allocated',
        'Allocated_At',
    ];

    protected $casts = [
        'Amount_Allocated' => 'decimal:2',
        'Allocated_At' => 'date',
    ];

    // Cross-Database Relationships
    // ⚠️ Both relationships span different database connections

    /**
     * Get the recipient for this allocation (adam database - MySQL)
     * ⚠️ Cross-database relationship
     */
    public function recipient()
    {
        return $this->setConnection('adam')
            ->belongsTo(Recipient::class, 'Recipient_ID', 'Recipient_ID');
    }

    /**
     * Get the campaign for this allocation (izzati database - PostgreSQL)
     * ⚠️ Cross-database relationship
     */
    public function campaign()
    {
        return $this->setConnection('izzati')
            ->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }
}
