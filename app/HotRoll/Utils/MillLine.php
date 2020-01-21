<?php

namespace App\HotRoll\Utils;

class MillLine
{
    public static function getMillLines()
    {
        return ['2250', '1580'];
    }
    
    public static function getCoilIdHeader($line)
    {
        $header = '';
        switch ($line) {
            case '2250':
                $header = "H";
                break;
            case '1580':
                $header = "M";
                break;            
            default:
                $header = "";
                break;
        }

        return $header;
    }

    public static function getLineByCoilId($coilId)
    {
        $line = '';
        $header = substr($coilId, 0, 1);
        switch ($header) {
            case 'H':
                $line = '2250';
                break;
            case 'M':
                $line = '1580';
                break;            
            default:
                $line = '';
                break;
        }

        return $line;
    }

    public static function filterData($data, $key, $aimLine){
        $cond = function( $element ) use ($key, $aimLine) {
            $curLine = static::getLineByCoilId($element[ $key]);
            return $curLine === $aimLine;
        };
        return array_filter($data, $cond);
    }
}