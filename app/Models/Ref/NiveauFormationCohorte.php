<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NiveauFormationCohorte extends Model
{
    use HasFactory;

    protected $table = "NIVEAU_FORMATION_COHORTES";

    protected $fillable = ["ID_COHORTE", "ID_NIVEAU_FORMATION", "NOMBRE_ETUDIANT", "ACTIF" ,"UTI_CREE", "UTI_MODIFIE"];

    protected $connection = 'oracle';

    public $timestamps = false;
}
