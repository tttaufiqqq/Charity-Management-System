<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    // Table name (because your table is not plural)
    protected $table = 'donation';

    // Primary key name
    protected $primaryKey = 'Donation_ID';

    // Laravel won't auto-increment if PK isn't "id", but yours is still auto-increment
    public $incrementing = true;

    // PK is integer (important for non-default PKs)
    protected $keyType = 'int';

    // Mass assignable columns
    protected $fillable = [
        'Donor_ID',
        'Campaign_ID',
        'Amount',
        'Donation_Date',
        'Payment_Method',
        'Receipt_No',
    ];

    /**
     * Relationships
     */

    // Relationships
    public function donor()
    {
        return $this->belongsTo(Donor::class, 'Donor_ID', 'Donor_ID');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }
}
