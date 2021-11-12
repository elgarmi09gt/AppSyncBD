<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domaine extends Model
{
    use HasFactory;

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
