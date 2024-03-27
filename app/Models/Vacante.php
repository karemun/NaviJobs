<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacante extends Model
{
    use HasFactory;

    protected $casts = ['ultimo_dia' => 'date']; // En lugar de string es una fecha

    protected $fillable = [
        'titulo',
        'salario_id',
        'categoria_id',
        'empresa',
        'ultimo_dia',
        'descripcion',
        'imagen',
        'user_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class); // Pertenece a categoria
    }

    public function salario()
    {
        return $this->belongsTo(Salario::class); // Pertenece a salario
    }

    public function cadidatos()
    {
        return $this->hasMany(Candidato::class)->orderBy('created_at', 'DESC');
    }

    public function reclutador()
    {
        // Relacion hacia el reclutador que publico la vacante
        return $this->belongsTo(User::class, 'user_id');
    }
}
