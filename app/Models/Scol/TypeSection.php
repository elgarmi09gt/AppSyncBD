<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSection extends Model
{
    use HasFactory;

    protected $connection = 'oracleScol';

    protected $table = 'Etablissements';

}
