<?php

namespace App\Models\Scol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationEtude extends Model
{
    use HasFactory;

    protected $table = 'ORGANISATION_ETUDES';

    protected $connection = 'oracleScol';
}
