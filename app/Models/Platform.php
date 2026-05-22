<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $table = 'platform';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'name',
        'icon_imagekit_url',
        'id_category_smm',
    ];

    public function category()
    {
        return $this->belongsTo(CategorySmm::class, 'id_category_smm');
    }
}
