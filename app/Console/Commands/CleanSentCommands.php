<?php

namespace App\Console\Commands;

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Tobuli\Entities\SentCommand;

class CleanSentCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sent_commands:clean {--date=} {--days=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent commands cleaner';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $processManager = new ProcessManager('sent_commands:clean');

        if (!$processManager->canProcess()) {
            $this->line('Cant process');

            return Command::FAILURE;
        }

        $query = $this->getQuery();

        if ($query === null) {
            $this->error('--date or --days option missing');

            return Command::FAILURE;
        }

        do {
            $deleted = $query->limit(10000)->delete();
            sleep(1);
        } while ($deleted > 0);

        $this->line('Job done');

        return Command::SUCCESS;
    }

    private function getQuery(): ?Builder
    {
        $query = SentCommand::query();

        return match (true) {
            (bool)$this->option('date') => $query->where('created_at', '<=', $this->option('date')),
            (bool)$this->option('days') => $query->where('created_at', '<=', Carbon::now()->subDays($this->option('days'))),
            default => null,
        };
    }
}
