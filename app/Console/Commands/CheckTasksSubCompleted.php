<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class CheckTasksSubCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach(Task::all() as $tasks){
            $total_st = $tasks->subTasks()->count();
            if($total_st){
                $total_completed_st = $tasks->subTasks()->where([ 'status' => 3 ])->count();
                if($total_completed_st == $total_st){
                    $tasks->status = 3;
                    $tasks->save();
                    $tasks->refresh();
                }
            }
        }
    }
}
