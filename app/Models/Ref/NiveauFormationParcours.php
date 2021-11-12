<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NiveauFormationParcours extends Model
{
    use HasFactory;

    protected $table = "Niveaux_formation_parcours";
/**
 * ID_NIVEAU_FORMATION_PARCOURS "ID_NIVEAU_FORMATION_COHORTE", "ID_FORMATION_OPTION", "ID_FORMATION_SPECIALITE", "UTI_CREE", "UTI_MODIFIE", "VALIDATION_DAP"
 */
    protected $fillable = ["ID_NIVEAU_FORMATION_COHORTE", "ID_FORMATION_OPTION", "ID_FORMATION_SPECIALITE", "UTI_CREE", "UTI_MODIFIE", "VALIDATION_DAP"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
