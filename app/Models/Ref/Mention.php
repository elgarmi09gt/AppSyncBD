<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","ID_DOMAINE","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
