<?php

namespace App\Console\Commands;

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\SentCommand;
use Tobuli\Services\Commands\SendCommandService;

class SendPendingCommands extends Command
{
    private const ATTEMPTS_MAX = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commands:send_pending {loop?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send commands which are in the pending queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $processManager = new ProcessManager('commands:send_pending');

        if (!$processManager->canProcess()) {
            echo "Cant process \n";

            return Command::FAILURE;
        }

        SentCommand::where('status', SentCommand::STATUS_PENDING)
            ->where('updated_at', '<', \Carbon::now()->subDays(7))
            ->update(['status' => SentCommand::STATUS_FAIL]);

        $sendCommandService = new SendCommandService();

        $loop = $this->argument('loop');

        do {
            SentCommand::with(['device', 'device.traccar', 'user'])
                ->whereHas('device', fn (Builder $query) => $query->online(1)->select('devices.id'))
                ->where('status', SentCommand::STATUS_PENDING)
                ->chunk(200, function ($sentCommands) use ($sendCommandService) {
                    foreach ($sentCommands as $cmd) {
                        if ($cmd->updated_at->gte($cmd->device->server_time)) {
                            continue;
                        }

                        $sendCommandService->sendStored($cmd, self::ATTEMPTS_MAX);
                    }
                });

            if ($loop) {
                sleep(5);
            }
        } while($processManager->canProcess() && $loop);

        return Command::SUCCESS;
    }
}
