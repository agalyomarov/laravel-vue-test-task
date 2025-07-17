<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class CleanUpTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:tasks {--date_lte=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление задач со статусом "backlog" старше указанной даты';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date_lte')
            ? now()->parse($this->option('date_lte'))
            : now()->subDays(30);

        $count = Task::where('status', 'backlog')
            ->where('created_at', '<=', $date)
            ->delete();

        $msg = "Удалено задач: $count (до {$date->toDateString()})";
        Log::channel('cleanup')->info($msg);

        $this->info($msg);
    }
}
