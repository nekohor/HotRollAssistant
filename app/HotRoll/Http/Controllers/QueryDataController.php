<?php

namespace App\HotRoll\Http\Controllers;

use App\HotRoll\Dao\MesResultDao;

use App\Backend\Http\JsonResponse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QueryDataController extends Controller
{
    public function getMesReuslt(Request $request) {

        $dao = new MesResultDao();

        if ( $request->has('coilId') ) {

            $coilId = $request->query("coilId");
            $data = $dao->getDataByCoilId($coilId);

        } else if ( $request->has(['startDate', 'endDate']) ) {

            $startDate = $request->query("startDate");
            $endDate = $request->query("endDate");

            $line = '1580';
            $data = $dao->getDataByLineAndTime($line, $startDate, $endDate);

        }
        
        return response()->json(
            new JsonResponse([
                "result" => $data, 
            ])
        );
    }
        
}
