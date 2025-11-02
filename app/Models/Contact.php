<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{    
    use HasFactory;

    protected $fillable = [
        'debtor_id',
        'tipo',
        'categoria',
        'detalle',
        'validacion',
        'fecha_validacion',
        'principal',
        'observacion',
    ];

    protected $casts = [
        'validacion' => 'boolean',
        'principal' => 'boolean',
        'fecha_validacion' => 'datetime',
    ];

    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }
}