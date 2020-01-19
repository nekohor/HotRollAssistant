<?php

namespace App\HotRoll\Http\Controllers;

use App\HotRoll\Logics\Production\ShiftOutputLogic;

use App\Backend\Http\JsonResponse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionOutputController extends Controller
{
    public function calculateShiftOutput(Request $request)
    {
        $line = $request->input("line");
        $inputData = $request->input("inputData");

        // $inputData = json_decode($inputData, true);
        // dd($inputData);

        $bll = new ShiftOutputLogic();
        $bll->setLine($line);
        $bll->setData($inputData);

        $resultData = $bll->getResult();
        $resultHeader = $bll->getResultHeader();

        return response()->json(
            new JsonResponse([
                'line' => $line, 
                'resultData'=> $resultData,
                'resultHeader' => $resultHeader,
                'count' => count($inputData),
            ])
        );
    }
}
