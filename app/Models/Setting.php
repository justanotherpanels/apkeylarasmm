<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'site_name',
        'favicon_path',
        'logo_path',
        'email',
        'instagram_url',
        'facebook_url',
        'whatsapp_url',
        'head_code',
        'footer_code',
    ];
}
