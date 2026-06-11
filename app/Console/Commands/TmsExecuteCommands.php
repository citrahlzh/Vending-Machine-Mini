<?php

namespace App\Console\Commands;

use App\Services\TMS\CommandExecutorService;
use Illuminate\Console\Command;

class TmsExecuteCommands extends Command
{
    protected $signature   = 'tms:execute-commands';
    protected $description = 'Proses satu command dari antrian TMS';

    public function handle(CommandExecutorService $service): void
    {
        $service->processNext();
    }
}
