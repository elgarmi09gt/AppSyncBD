<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NiveauFormation extends Model
{
    use HasFactory;

    protected $table = "NIVEAUX_SECTIONS";

    protected $connection = 'oracleScol';
}
