<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationEtude extends Model
{
    use HasFactory;

    protected $table = 'ORGANISATION_ETUDES';

    protected $connection = 'oracle';

    public $timestamps = false;
}
