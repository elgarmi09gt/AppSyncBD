<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSection extends Model
{
    use HasFactory;

    protected $connection = 'oracle';

    protected $fillable = ["LIBELLE_LONG","LIBELLE_COURT","UTI_CREE","UTI_MODIFIE"];

    public $timestamps = false;

    protected $table = 'type_sections';
}
