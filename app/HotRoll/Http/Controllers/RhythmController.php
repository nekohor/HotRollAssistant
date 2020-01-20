<?php

namespace App\HotRoll\Http\Controllers;

use App\HotRoll\Logics\Production\RhythmLogic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Backend\Http\JsonResponse;


class RhythmController extends Controller
{
    public function getDischargeRhythms(Request $request)
    {   
        $line = '1580';
        // $startDate = $request->query("startDate");
        // $endDate = $request->query("endDate");

        $startTime = '20200119000000';
        $endTime   = '20200120000000';

        $logic = new RhythmLogic($line);

        $data = $logic->getDischargeRhythms($startTime, $endTime);

        dd($data);


    }

    public function getExampleDischargeRhythms(Request $request)
    {   
        $line = '1580';
        $startTime = '20200119000000';
        $endTime   = '20200120000000';

        $logic = new RhythmLogic($line);
        $data = $logic->getDischargeRhythms($startTime, $endTime);

        dd($data);
    }
}