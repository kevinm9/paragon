<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{

        use HasFactory;


    protected $fillable = [
        'tipo_cedente',
        'nombre_cedente',
        'nombre_deudor',
        'monto_deuda_total',
        'diagnostico',
        'estado',
        'judicial',
        'ultima_gestion',
        'tipo_ultima_gestion',
        'proxima_gestion',
    ];

    protected $casts = [
        'judicial' => 'boolean',
        'ultima_gestion' => 'datetime',
        'proxima_gestion' => 'datetime',
    ];

        public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
