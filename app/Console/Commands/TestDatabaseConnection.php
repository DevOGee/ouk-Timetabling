<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the database connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing database connection...');
        
        try {
            DB::connection()->getPdo();
            $this->info('Successfully connected to the database!');
            $this->info('Database Name: ' . DB::connection()->getDatabaseName());
            return 0;
        } catch (Exception $e) {
            $this->error('Could not connect to the database.');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
