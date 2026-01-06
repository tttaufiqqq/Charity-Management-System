<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: hannah (MySQL)
     * Tables: donor, donation, donation_allocation
     */
    protected $connection = 'hannah';

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
        'Payment_Status', // ToyyibPay payment status
        'Bill_Code', // ToyyibPay bill code
        'Transaction_ID', // ToyyibPay transaction ID
    ];

    protected $casts = [
        'Donation_Date' => 'datetime',
        'Amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */

    /**
     * Get the donor who made this donation (same database - hannah)
     */
    public function donor()
    {
        return $this->belongsTo(Donor::class, 'Donor_ID', 'Donor_ID');
    }

    /**
     * Get the campaign this donation belongs to (izzati database - PostgreSQL)
     * ⚠️ Cross-database relationship
     */
    public function campaign()
    {
        return $this->setConnection('izzati')
            ->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }
}
