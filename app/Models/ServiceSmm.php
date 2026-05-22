<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSmm extends Model
{
    use HasFactory;

    protected $table = 'service_smm';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'id_category_smm',
        'id_api_smm',
        'name_service',
        'pid',
        'min_order',
        'max_order',
        'price_api',
        'price_sale',
        'price_reseller',
        'type',
        'desc',
        'refill',
        'status',
    ];

    protected $casts = [
        'refill' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(CategorySmm::class, 'id_category_smm');
    }

    public function api()
    {
        return $this->belongsTo(ApiSmm::class, 'id_api_smm');
    }
}
