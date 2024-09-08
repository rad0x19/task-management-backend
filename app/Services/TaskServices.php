<?php

namespace App\Services;

use App\Models\Task;

class TaskServices {

    // create
    public function createProcess($isDraft, $title, $priority, $description, $subTasks, $attachments){
        $user = auth()->user();
        $task = $user->tasks()->create([
            'title'         =>  $title,
            'priority'      =>  $priority,
            'description'   =>  $description,
            'isDraft'       =>  $isDraft !== "false" ? true : false
        ]);
        // create sub-tasks
        if($subTasks && count($subTasks)){
            for($i = 0; $i < count($subTasks); $i++){
                $t = $subTasks[$i];
                $task->subTasks()->create([
                    'user_id'       =>  $user->id,
                    'title'         =>  $t['title'],
                    'priority'      =>  $t['priority'],
                    'description'   =>  $t['description'],
                    'isDraft'       =>  $isDraft !== "false" ? true : false
                ]);
            }
        }
        // upload and link attachments
        if($attachments != null && count($attachments)){
             for($i = 0; $i < count($attachments); $i++){
                $task->attachments()->create([
                    'file_name'     =>  $attachments[$i]->store('public/attachments')
                ]);
             }
        }
    }

    // list & search
    public function listAndSearchProcess($sort_by = 'priority_asc', $perPage = 9, $display = 'all', $search_title = null){
        $user = auth()->user();
        $tasks = $user->tasks()->where('task_id', '=', null);
        // sort process
        if($sort_by == 'priority_desc'){
            $tasks = $tasks->orderBy('priority', 'desc');
        }else if($sort_by == 'title_asc'){
            $tasks = $tasks->orderBy('title', 'asc');
        }else if($sort_by == 'title_desc'){
            $tasks = $tasks->orderBy('title', 'desc');
        }else if($sort_by == 'date_asc'){
            $tasks = $tasks->orderBy('created_at', 'asc');
        }else if($sort_by == 'date_desc'){
            $tasks = $tasks->orderBy('created_at', 'desc');
        }else{
            $tasks = $tasks->orderBy('priority', 'asc');
        }
        // display by process
        if($display == 'todo'){
            $tasks = $tasks->where([ 'status' => 1 ]);
        }else if($display == 'inprogress'){
            $tasks = $tasks->where([ 'status' => 2 ]);
        }else if($display == 'done'){
            $tasks = $tasks->where([ 'status' => 3 ]);
        }else{
            $tasks = $tasks;
        }
        // search by title
        if($search_title !== 'null'){
            $tasks = $tasks->where('title', 'LIKE', '%'.$search_title.'%');
        }
        return $tasks->paginate($perPage);
    }

    // mark as process
    public function markAsProcess($status,$id){
        $user = auth()->user();
        $task = Task::whereId($id)->first();
        if(!$task){
            throw new Exception('Task not found');
        }
        if($task->user_id != $user->id){
            throw new Exception('Task is not accessible.');
        }
        if($status == 3){
            $task->subTasks()->update([
                'status'    =>  3
            ]);
        }
        // check if task is sub
        if($task->task_id != null){
            $main = Task::whereId($task->task_id)->first();
            $total_st = $main->subTasks()->count();
            $total_completed_st = $main->subTasks()->where([ 'status' => 3 ])->count();
            if($total_st == $total_completed_st && $total_st > 0) {
                $main->status = 3;
                $main->save();
                $main->refresh();
            }
        }
        $task->status = $status;
        $task->save();
        $task->refresh();
    }

}