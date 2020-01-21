<?php

namespace App\HotRoll\Logics\Production;

use App\HotRoll\Dao\MesResultDao;

use App\HotRoll\Dao\SteelGradeCategoDao;
use App\HotRoll\Dao\HourlyOutputDao;


use Carbon\Carbon;

class RhythmLogic
{
    private $line;
    private $mesResultDao;

    private $categoDao;
    private $hourlyOutputDao;

    public function __construct()
    {
        $this->mesResultDao = new MesResultDao();
        $this->categoDao = new SteelGradeCategoDao();
        $this->hourlyOutputDao = new HourlyOutputDao();
    }

    public function getAimDischargeRhythm($line, $record)
    {
        $steelGrade = $record['steelGrade'];
        $catego = $this->categoDao->getCategoBySteelGrade($steelGrade);

        $thk = $record['thk'];
        $wid = $record['wid'];

        $data = $this->hourlyOutputDao->getHourlyOutput(
            $line, $catego, $thk, $wid);

        if (count($data) === 0) {
            $pieces = 28;
        } else {
            $pieces = $data[0]["pieces_an_hour"];
        }
        return $this->getSecondsPerPiece($pieces);        
    }

    public function getSecondsPerPiece($piece)
    {
        return 3600.0 / $piece;
    }

    public function getActDischargeRhythm($record, $key, $data)
    {
        if ($key <= 0) {
            return 120;
        } else {

            $curRecord =  $data[$key];
            $prevRecord = $data[$key - 1];
            
            $curTime = Carbon::createFromFormat('YmdHis', $curRecord['dischargeTime']);
            $prevTime = Carbon::createFromFormat('YmdHis', $prevRecord['dischargeTime']);

            $rhythmSeconds = $curTime->diffInSeconds($prevTime);
            return $rhythmSeconds;
        }
    }

    public function getDischargeRhythms($line, $startTime, $endTime)
    {
        $data = $this->mesResultDao->getDataByLineAndTime($line, $startTime, $endTime);
        
        $rhythms = [];
        foreach ($data as $key => $record) {

            $rhythm = [];
            $rhythm["coilId"] = $record['coilId'];
            $rhythm["dischargeTime"] = $record['dischargeTime'];
            $rhythm["aimDischargeRhythm"] = round($this->getAimDischargeRhythm($line, $record), 2);
            $rhythm["actDischargeRhythm"] = $this->getActDischargeRhythm($record, $key, $data);

            $rhythms[]= $rhythm;
        }

        return $rhythms;
    }




}