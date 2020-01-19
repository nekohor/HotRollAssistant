<?php

namespace App\HotRoll\Imports;

use App\HotRoll\Models\RuleSteelGradeCatego;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuleSteelGradeCategoImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new RuleSteelGradeCatego([
            'steel_grade' => $row['steel_grade'],
            'catego1' => $row['catego1'],
            'catego2' => $row['catego2'],
        ]);
    }
}
