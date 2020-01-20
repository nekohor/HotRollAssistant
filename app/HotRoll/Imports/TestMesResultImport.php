<?php

namespace App\HotRoll\Imports;

use App\HotRoll\Models\TestMesResult;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TestMesResultImport implements ToModel, WithHeadingRow, WithProgressBar, WithBatchInserts, WithChunkReading
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TestMesResult([
            'ACTCOILID' => $row['actcoilid'],
            'PRODSTART' => $row['prodstart'],
            'PRODEND' => $row['prodend'],
            'ACTSLABID' => $row['actslabid'],
            'GRADENAME' => $row['gradename'],
            'HEXIT' => $row['hexit'],
            'SLABLENGTH' => $row['slablength'],
            'SLABWIDTH' => $row['slabwidth'],
            'SLABTHICKNESS' => $row['slabthickness'],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
