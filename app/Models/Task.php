<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'title',
        'description',
        'status',
        'priority',
        'isDraft'
    ];

    protected $with = [
        'attachments',
        'subTasks'
    ];

    protected $appends = [
        'completed_sub_tasks',
        'sub_tasks_count'
    ];

    // set value to completed_sub_tasks
    public function getCompletedSubTasksAttribute(){
        $count = self::subTasks()->where([ 'status' => 3 ])->count();
        return $count;
    }

    // set value to sub_tasks_count
    public function getSubTasksCountAttribute(){
        return self::subTasks()->count();
    }

    // belongs to user
    public function user(){
        return $this->belongsTo(User::class);
    }

    // subTask
    public function subTask(){
        return $this->belongsTo(self::class);
    }

    // sub-tasks
    public function subTasks(){
        return $this->hasMany(self::class)->orderBy('priority', 'desc');
    }

    // attachments
    public function attachments(){
        return $this->hasMany(TaskAttachment::class);
    }

}
