<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeFormation extends Model
{
    use HasFactory;

    protected $table = 'TYPE_FORMATION';

    protected $connection = 'oracleScol';
}
