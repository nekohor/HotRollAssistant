<?php

namespace App\HotRoll\Dao;

use App\HotRoll\Models\RuleSteelGradeCatego;

class SteelGradeCategoDao
{
    public function getCategoBySteelGrade($steelGrade)
    {
        $data = RuleSteelGradeCatego::where("steel_grade", $steelGrade)->get();

        if (count($data) <= 0) {
            return "普碳商品材";
        } else {
            return $data[0]["catego2"];
        }
        
        
    }
}