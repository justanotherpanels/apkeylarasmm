<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDeposit extends Model
{
    use HasFactory;

    protected $table = 'history_deposit';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'id_user',
        'invoice',
        'amount',
        'status_payment',
        'detail_transaction'
    ];

    protected $casts = [
        'detail_transaction' => 'array',
    ];

    /**
     * Get the user that owns the deposit history.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
