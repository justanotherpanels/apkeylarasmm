<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSmm extends Model
{
    use HasFactory;

    protected $table = 'order_smm';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'id_user',
        'id_service_smm',
        'id_api_smm',
        'sid',
        'invoice',
        'target',
        'amount',
        'price_api',
        'price_sale',
        'price_reseller',
        'status_order',
        'start_count',
        'remains',
        'refill',
    ];

    /**
     * Get the user that owns the SMM order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the service associated with the SMM order.
     */
    public function service()
    {
        return $this->belongsTo(ServiceSmm::class, 'id_service_smm');
    }

    /**
     * Get the SMM API provider associated with the order.
     */
    public function api()
    {
        return $this->belongsTo(ApiSmm::class, 'id_api_smm');
    }
}
