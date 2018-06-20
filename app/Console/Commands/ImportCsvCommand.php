<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportCsvService;

class ImportCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {csvFilePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import from csv file';

    private $service;


    public function __construct(ImportCsvService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $csvFilePath = $this->argument('csvFilePath');

        try {
            $data  = $this->service->import($csvFilePath);
            $count = $this->service->save($data);

            $this->info("$count records saved");

            return true;
        } catch (\DomainException $e) {
            $this->error($e->getMessage());

            return false;
        }
    }
}
