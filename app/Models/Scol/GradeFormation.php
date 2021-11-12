<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeFormation extends Model
{
    use HasFactory;

    protected $connection = 'oracleScol';

    protected $table = "GRADE_DIPLOME_EDIT";

}
