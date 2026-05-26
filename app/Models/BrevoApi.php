<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrevoApi extends Model
{
    use HasFactory;

    protected $table = 'brevo_api';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'status',
        'api_config'
    ];

    protected $casts = [
        'api_config' => 'array',
    ];
}
