<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeFormation extends Model
{
    use HasFactory;

    protected $table = "grades_formation";

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
