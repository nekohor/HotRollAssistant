<?php

namespace App\HotRoll\Dao;

use App\HotRoll\Models\RuleHourlyOutput;

class HourlyOutputDao
{
    public function getHourlyOutput($line, $catego, $thk, $wid)
    {
        $data = RuleHourlyOutput::where("line", $line)
        ->where("steel_grade_catego", $catego)
        ->where("thk_gte", "<=", $thk)
        ->where("thk_lt", ">", $thk)
        ->where("wid_gte", "<=", $wid)
        ->where("wid_lt", ">", $wid)
        ->get();

        return $data;
    }
}