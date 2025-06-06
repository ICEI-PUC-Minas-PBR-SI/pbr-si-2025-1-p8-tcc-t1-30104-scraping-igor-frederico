<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'patent_id',
        'claim',
    ];

    /**
     * Relacionamento com a patente
     */
    public function patente()
    {
        return $this->belongsTo(Patente::class, 'patent_id');
    }
}
