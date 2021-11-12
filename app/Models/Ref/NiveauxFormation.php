<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NiveauxFormation extends Model
{
    use HasFactory;

    protected $table = "NIVEAUX_FORMATIONS";

    //LibelleLong, UtiCree, LibelleCourt, IdNiveau, IdFormation, ASelection, AutorisationPermise, Presentielle, NbreInsPermise, Diplomante, Ouvert
    protected $fillable = ["ID_NIVEAU", "ID_FORMATION", "LIBELLE_LONG", "LIBELLE_COURT" ,"A_SELECTION", "AUTORISATION_PERMISE", "UTILISE_EVALUATION", "PRESENTIELLE", "NBRE_INS_PERMISE",
                        "DIPLOMANTE", "OUVERT", "TAG_SEMESTRIALISATION", "TAG_GROUPE_MATIERE", "DATE_CREATION", "DATE_MODIFICATION", "UTI_CREE", "UTI_MODIFIE", "MODE_DISPATCHING"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
