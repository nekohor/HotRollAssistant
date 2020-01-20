<?php

namespace App\HotRoll\Utils;

class MillLine
{
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
}