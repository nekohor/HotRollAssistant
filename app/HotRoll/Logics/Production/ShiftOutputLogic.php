<?php

namespace App\HotRoll\Logics\Production;

use App\HotRoll\Dao\SteelGradeCategoDao;
use App\HotRoll\Dao\HourlyOutputDao;

class ShiftOutputLogic
{
    private $line;
    private $data;

    private $planIds;

    public function __construct()
    {

    }

    public function setLine(string $line)
    {
        $this->line = $line;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getMaxShiftTime($shiftType, $changeRollTimes)
    {
        $totalShiftTime = 3600 * 8;
        $routineCheckTime = 50 * 60;

        $changeRollSecondsPerTime = 15 * 60;
        $changeRollTime = $changeRollSecondsPerTime * $changeRollTimes;

        switch ($shiftType) {
            case '大夜班':
                $maxShiftTime = $totalShiftTime - $changeRollTime;
                break;
            case '白班':
                $maxShiftTime = $totalShiftTime - $changeRollTime - $routineCheckTime;
                break;
            case '小夜班':
                $maxShiftTime = $totalShiftTime - $changeRollTime;
                break;                
            default:
                $maxShiftTime = $totalShiftTime - $changeRollTime;
                break;
        }
        return $maxShiftTime;
    }

    public function getResult()
    {
        $result = [];

        $shiftTypes = ["大夜班", "白班", "小夜班"];
        $changeRollTimesArr = [1, 2, 3];

        foreach ($shiftTypes as $shiftType) {
            foreach ( $changeRollTimesArr as $changeRollTimes) {

                $record = [];
                $record["班次"] = $shiftType;
                $record["换辊次数"] = $changeRollTimes;

                $maxShiftTime = $this->getMaxShiftTime($shiftType, $changeRollTimes);
                $record["预计班组产量"] = $this->getYieldNum($maxShiftTime);

                $result []= $record;

            }
        }
        return $result;
        
    }

    public function getResultHeader()
    {
        return ["班次", "换辊次数", "预计班组产量"];
    }


    public function getYieldNum($maxShiftTime)
    {
        $this->planIds = [];
        $lastIdx = -1;
        $shiftAccTime = 0.0;
        
        foreach ($this->data as $index => $record) {
            $planId = $record["planId"];
            $piece = $this->getPiecesAnHour($record);
            $seconds = $this->getSecondsPerPiece($piece);

            $nextAccTime = $shiftAccTime + $seconds;

            if ( $nextAccTime > $maxShiftTime ) {
                $lastIdx = $index;
                break;
            } else {
                $shiftAccTime = $nextAccTime;
            }

            if ( in_array($planId, $this->planIds)) {
                ;;
            } else {
                $this->planIds []= $planId;
            }
        }

        
        if ($lastIdx === -1) {
            $yieldNum = count($this->data);
        } else {
            $yieldNum = $lastIdx + 1;
        }
        return $yieldNum;

    }

    public function getPiecesAnHour($record)
    {
        $steelGrade = $record['steelGrade'];
        $thk = $record['thk'];
        $wid = $record['wid'];

        $categoDao = new SteelGradeCategoDao();
        $catego = $categoDao->getCategoBySteelGrade($steelGrade);

        $hourlyOutputDao = new HourlyOutputDao();
        $data = $hourlyOutputDao->getHourlyOutput(
            $this->line, $catego, $thk, $wid);

        if (count($data) === 0) {
            $pieces = 28;
        } else {
            $pieces = $data[0]["pieces_an_hour"];
        }
        return $pieces;
    }

    public function getSecondsPerPiece($piece)
    {
        return 3600.0 / $piece;
    }

}

