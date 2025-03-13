<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estado extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'descripcion',
    ];
    
    public function agencias()
    {
        return $this->hasMany(Agencia::class);
    }
}