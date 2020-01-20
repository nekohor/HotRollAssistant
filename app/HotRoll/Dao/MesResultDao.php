<?php
namespace App\HotRoll\Dao;

use App\HotRoll\Models\TestMesResult;

use App\HotRoll\Utils\MillLine;
use MesDB;

class MesResultDao
{
    private $slabTableName;
    private $coilTableName;
    private $selectedColumns;

    public function __construct()
    {

        $this->slabTableName = "RSLAB_RESULT";
        $this->coilTableName = "RHS_RESULT";

        // SELECT * FROM RHS_RESULT LEFT JOIN RSLAB_RESULT on RHS_RESULT.ACTSLABID = RSLAB_RESULT.SLAB_ID where PRODSTART >= '20200101000000' and PRODEND <= '20200121000000'

        $this->selectedColumns = array_merge(
            $this->getSelectedSlabColumns(),
            $this->getSelectedCoilColumns()
        );
    }

    public function getSelectedSlabColumns()
    {
        $columns = [
            "SLABLENGTH",
            "SLABWIDTH",
            "SLABTHICKNESS",
        ];
        return $this->attachTableNameTo($columns, $this->slabTableName);
    }

    public function getSelectedCoilColumns()
    {
        $columns = [
            "ACTCOILID",
            "PRODSTART",
            "PRODEND",
            "ACTSLABID",
            "GRADENAME",
            "HEXIT",
        ];
        return $this->attachTableNameTo($columns, $this->coilTableName);

    }

    public function attachTableNameTo($columns, $tableName)
    {
        $addTableName = function($col) use ($tableName) {
            return $tableName . "." . $col;
        };

        $attachedColumns = array_map($addTableName, $columns);
        return $attachedColumns;
    }

    public function getJoin()
    {
        $join = [
            "[>]".$this->slabTableName  => ["ACTSLABID" => "SLAB_ID"],
        ];
        return $join;
    }

    public function getWhereByCoilId($coilId)
    {
        $conds = [
            $this->coilTableName ."." . "ACTCOILID[=]" => $coilId,
        ];
        return $conds;
    }

    public function getWhereByTime($startTime, $endTime)
    {
        $conds = [
            "AND" => [
                $this->coilTableName ."." . "PRODSTART[>=]" => $startTime,
                $this->coilTableName ."." . "PRODEND[<=]" => $endTime,
            ]
        ];
        return $conds;
    }

    public function getWhereByLineAndTime($line, $startTime, $endTime)
    {
        $pattern = MillLine::getCoilIdHeader($line) . '%';
        $conds = [
            "AND" => [
                $this->coilTableName ."." . "ACTCOILID[~]" => $pattern,
                $this->coilTableName ."." . "PRODSTART[>=]" => $startTime,
                $this->coilTableName ."." . "PRODEND[<=]" => $endTime,
            ]
        ];
        return $conds;
    }


    public function getDataByTime($startTime, $endTime)
    {

        $data = MesDB::select(
            $this->coilTableName, 
            $this->getJoin(),
            $this->selectedColumns,
            $this->getWhereByTime($startTime, $endTime)
        );

        return $this->postProcess($data);
    }

    public function getDataByLineAndTime($line, $startTime, $endTime)
    {

        $data = MesDB::select(
            $this->coilTableName, 
            $this->getJoin(),
            $this->selectedColumns,
            $this->getWhereByLineAndTime($line, $startTime, $endTime)
        );

        return $this->postProcess($data);
    }

    public function getTestDataByLineAndTime($line, $startTime, $endTime)
    {
        $pattern = MillLine::getCoilIdHeader($line) . '%';
        $data = TestMesResult::where([
            ['ACTCOILID', 'like', $pattern],
            ['PRODSTART', '>=', $startTime],
            ['PRODEND', '<=', $endTime],
        ])->get();

        return $this->postProcess($data);
    }

    public function getDataByCoilId($coilId)
    {
        $data = MesDB::select(
            $this->coilTableName, 
            $this->getJoin(), 
            $this->selectedColumns,
            $this->getWhereByCoilId($coilId)
        );
        return $this->postProcess($data);
    }

    public function postProcess($rawData)
    {
        $data = $this->getWashedData($rawData);
        $coilIds = array_column($data, 'coilId');
        array_multisort($coilIds, SORT_STRING, SORT_ASC, $data);
        return $data;
    }

    public function getWashedData($rawData)
    {   
        $data = [];
        foreach ($rawData as $key => $row) {
            $record = $this->getRecord($row);
            $data []= $record;
        }
        return $data;
    }

    public function getRecord($row)
    {
        $record = [];
        $record['steelGrade'] = $row['GRADENAME'];
        $record['thk'] = $row['HEXIT'];
        $record['wid'] = $row['SLABWIDTH'];
        $record["coilId"] = $row['ACTCOILID'];
        $record['dischargeTime'] =  $row['PRODSTART'];

        return $record;
    }

    public function getExample()
    {

        $data = MesDB::select("RHS_RESULT", [
            "ACTCOILID", "PRODSTART", "PRODEND", "ACTSLABID", "GRADENAME", "HEXIT",
        ], [
            "AND" => [
                "PRODSTART[>=]" => "20200119120000",
                "PRODSTART[<=]" => "20200119140000",
            ]
        ]);
        return $data;
    }

}
