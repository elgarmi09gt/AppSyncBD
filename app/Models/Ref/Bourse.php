<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bourse extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
}
