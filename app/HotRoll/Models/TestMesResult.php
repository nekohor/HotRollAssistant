<?php

namespace App\HotRoll\Models;

use Illuminate\Database\Eloquent\Model;

class TestMesResult extends Model
{
    protected $fillable = [

        "ACTCOILID",
        "PRODSTART",
        "PRODEND",
        "ACTSLABID",
        "GRADENAME",
        "HEXIT",

        "SLABLENGTH",
        "SLABWIDTH",
        "SLABTHICKNESS",
        
    ];
}
