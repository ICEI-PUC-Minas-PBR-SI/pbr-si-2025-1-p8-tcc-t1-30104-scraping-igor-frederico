<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patente extends Model
{
    use HasFactory;

    protected $table = 'patents'; // Nome da tabela

    protected $fillable = [
        'farmaco',
        'document_id',
        'date_published',
        'title',
        'patent_number',
        'page_count',
        'type',
    ];

    /**
     * Relacionamento com inventores
     */
    public function inventors()
    {
        return $this->hasMany(Inventor::class, 'patent_id');
    }

    /**
     * Relacionamento com claims
     */
    public function claims()
    {
        return $this->hasMany(Claim::class, 'patent_id');
    }
}
