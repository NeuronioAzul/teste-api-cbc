<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clubes extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['clube', 'saldo_disponivel'];
}
