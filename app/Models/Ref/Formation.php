<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","ID_MENTION","ID_HISTORIQUES_STRUCTURE","ID_GRADES_FORMATION","ID_CYCLE","ID_TYPE_FORMATION",
    "PROFESSIONALISANTE","TRONC_COMMUN","ID_ORGANISATION_ETUDE","JUSTIFICATION_PROGRAMME","ORGANISATION_PROGRAMME","PROFIL_ACADEMIQUE","PROFIL_PROFESSIONNEL","OBSERVATIONS",
    "PAYANTE","VALIDE","PRESENTIELLE","FORMATION_RECONNUE","OUVERT","UTI_CREE","UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
