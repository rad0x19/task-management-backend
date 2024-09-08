<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\TaskServices;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\markAsStatusRequests;
use App\Http\Requests\Tasks\TaskCreateOrUpdateRequests;

class TaskController extends Controller
{
    
    public $taskServices;

    public function __construct(){
        $this->taskServices = new TaskServices();
    }

    // create controller
    public function create(TaskCreateOrUpdateRequests $request){
        DB::beginTransaction();
        try {
            $this->taskServices->createProcess(
                $request->isDraft,
                $request->title,
                $request->priority,
                $request->description,
                $request->subTasks,
                $request->file('attachments') ?? null
            );
            DB::commit();
            return response()->json([
                'message'   =>  'task created success fully.'
            ]);
        }catch(Exception $err){
            DB::rollback();
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

    // list and search controller
    public function listAndSearch(Request $request){
        try{
            return $this->taskServices->listAndSearchProcess($request->sort_by, $request->perPage, $request->display, $request->search_title);
        }catch(Exception $err){
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

    // status update
    public function markAsFunction(markAsStatusRequests $request){
        DB::beginTransaction();
        try {
            $this->taskServices->markAsProcess($request->status, $request->id);
            DB::commit();
            return response()->json([
                'message'   =>  'Task Status updated.'
            ]);
        }catch(Exception $err){
            DB::rollback();
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

}
