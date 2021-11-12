<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;

    protected $table = "Structures";

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","ADRESSE","EMAIL","TELEPHONE","FAX",
                            "STRUCTURE_PARENT","ID_TYPE_SECTION","NIVEAU","URL","HORAIRE_TRAVAIL",
                            "UTI_CREE","UTI_MODIFIE","ID_SIGNATAIRE","SIGNATURE"];

    protected $connection = 'oracle';

    public $timestamps = false;

}
