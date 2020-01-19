<?php

namespace App\HotRoll\Models;

use Illuminate\Database\Eloquent\Model;

class RuleHourlyOutput extends Model
{
    protected $fillable = [
        'line',
        'steel_grade_catego',
        'thk_gte',
        'thk_lt',
        'wid_gte',
        'wid_lt',
        'pieces_an_hour',
    ];
}
