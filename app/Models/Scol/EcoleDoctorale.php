<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcoleDoctorale extends Model
{
    use HasFactory;

    protected $table = "ECOLE_DOCTORALES";

    protected $connection = 'oracleScol';
}
