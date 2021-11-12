<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domaine extends Model
{
    use HasFactory;

    protected $table = "SCOL_DOMAINE";

    protected $connection = 'oracleScol';
}
