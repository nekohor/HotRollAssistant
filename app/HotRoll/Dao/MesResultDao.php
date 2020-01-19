<?php
namespace App\HotRoll\Dao;

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
            "PRODSTART[>=]" => $startTime,
            "PRODEND[<=]" => $endTime,
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
        return $data;
    }

    public function getDataByCoilId($coilId)
    {
        $data = MesDB::select(
            $this->coilTableName, 
            $this->getJoin(), 
            $this->selectedColumns,
            $this->getWhereByCoilId($coilId)
        );
        return $data;
    }

    public function getExample()
    {

        $data = MesDB::select("RHS_RESULT", [
            "ACTCOILID", "PRODSTART", "PRODEND", "ACTSLABID", "GRADENAME", "HEXIT",
        ], [
            "ACTCOILID[=]" => "H110188900",
        ]);
        return $data;
    }

}
