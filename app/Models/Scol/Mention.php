<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;

    protected $table = "SCOL_MENTION";

    protected $connection = 'oracleScol';
}
