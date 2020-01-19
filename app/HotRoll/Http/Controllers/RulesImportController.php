<?php

namespace App\HotRoll\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\HotRoll\Models\RuleSteelGradeCatego;
use App\HotRoll\Models\RuleHourlyOutput;

use App\HotRoll\Imports\RuleSteelGradeCategoImport;
use App\HotRoll\Imports\RuleHourlyOutputImport;

use Maatwebsite\Excel\Facades\Excel;

class RulesImportController extends Controller
{
    public function importRules()
    {
        RuleSteelGradeCatego::truncate();
        Excel::import(new RuleSteelGradeCategoImport, 'rules/steel_grade_categos.xlsx');

        RuleHourlyOutput::truncate();
        Excel::import(new RuleHourlyOutputImport, 'rules/pieces_an_hour.xlsx');

        return redirect('/')->with('success', 'All good!');
    }
}
