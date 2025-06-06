<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventor extends Model
{
    use HasFactory;

    protected $fillable = [
        'patent_id',
        'name',
        'city',
        'state',
    ];

    /**
     * Relacionamento com a patente
     */
    public function patente()
    {
        return $this->belongsTo(Patente::class, 'patent_id');
    }
}
