<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","ID_SPECIALITE","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
