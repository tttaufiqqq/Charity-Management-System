<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationAllocation extends Model
{
    use HasFactory;

    protected $table = 'donation_allocation';

    // Composite primary key
    protected $primaryKey = ['Recipient_ID', 'Campaign_ID'];
    public $incrementing = false;

    protected $fillable = [
        'Recipient_ID',
        'Campaign_ID',
        'Amount_Allocated',
        'Allocated_At'
    ];

    protected $casts = [
        'Amount_Allocated' => 'decimal:2',
        'Allocated_At' => 'date',
    ];

    // Relationships
    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'Recipient_ID', 'Recipient_ID');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }
}
