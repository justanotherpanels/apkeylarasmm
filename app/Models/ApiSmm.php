<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSmm extends Model
{
    use HasFactory;

    protected $table = 'api_smm';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'name',
        'code',
        'balance',
        'url',
        'api_key',
        'status',
    ];
}
