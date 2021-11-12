<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    protected $table = "Historiques_structures";

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","ID_STRUCTURES","DATE_DEBUT","DATE_FIN","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
