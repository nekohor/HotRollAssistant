<?php

namespace App\Console\Commands;

use App\HotRoll\Imports\TestMesResultImport;
use Illuminate\Console\Command;

class ImportTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tests excel to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->title('Starting import');
        (new TestMesResultImport)->withOutput($this->output)->import('public/tests/test_mes_results.xlsx');
        $this->output->success('Import successful');
    }
}
