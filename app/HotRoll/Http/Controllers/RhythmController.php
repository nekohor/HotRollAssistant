<?php

namespace App\HotRoll\Http\Controllers;

use App\HotRoll\Logics\Production\RhythmLogic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\HotRoll\Utils\MillLine;

use App\Backend\Http\JsonResponse;


class RhythmController extends Controller
{
    public function getDischargeRhythms(Request $request)
    {   
        $line = $request->input("line");
        $startTime = $request->input("startTime");
        $endTime = $request->input("endTime");

        // $line = '2250';
        // $startTime = '20200120000000';
        // $endTime   = '20200121000000';

        $logic = new RhythmLogic();

        $data = $logic->getDischargeRhythms($line, $startTime, $endTime);

        $results = [];
        $results['coilIds'] = array_column($data, 'coilId');
        $results['expectedData'] = array_column($data, 'aimDischargeRhythm');
        $results['actualData'] = array_column($data, 'actDischargeRhythm');
        // dd($results);
        
        return response()->json(
            new JsonResponse([
                'line' => $line,
                'xAxisData'=> $results['coilIds'],
                'expectedData' => $results['expectedData'],
                'actualData' => $results['actualData'],
            ])
        );
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