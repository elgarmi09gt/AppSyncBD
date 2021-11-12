<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialite extends Model
{
    use HasFactory;

    protected $table = "SPECIALITE_DIPLOME_EDIT";

    protected $connection = 'oracleScol';
}
