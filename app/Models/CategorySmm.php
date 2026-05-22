<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySmm extends Model
{
    use HasFactory;

    protected $table = 'category_smm';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    public function platforms()
    {
        return $this->hasMany(Platform::class, 'id_category_smm');
    }

    public function services()
    {
        return $this->hasMany(ServiceSmm::class, 'id_category_smm');
    }
}
