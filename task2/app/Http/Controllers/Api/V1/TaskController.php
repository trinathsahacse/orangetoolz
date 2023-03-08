<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDetails;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $tasks = Task::orderby('id','desc')->with('details')->get();
            return $this->successResponse($tasks,'Task List', Response::HTTP_OK);
        }
        catch(Exception $e){
            return response()->json(['error'=>1, 'message'=>$e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task)
    {
        $post = $request->only('name','status');
        $data['task'] = $task->create($post);
        if($request->details && is_array($request->details)){
            foreach($request->details as $key => $details){
                $taskdetails = new TaskDetails();
                $taskdetails->task_id = $data['task']->id;
                $taskdetails->name = $details['name'];
                $taskdetails->description = $details['description'];
                $taskdetails->save();
            }
        }
        return $this->successResponse($data, 'Task Created', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $task = Task::where('id',$id)->with('details')->first();
            if(!$task){
                return response()->json(['error'=>1, 'message'=>'Task not found']);    
            }
            return $this->successResponse($task,'Task view', Response::HTTP_OK);
        }
        catch(Exception $e){
            return response()->json(['error'=>1, 'message'=>$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $task = Task::find($id);
            $task->name = $request->name;
            $task->status = $request->status;
            $task->save();
            if($request->details && is_array($request->details)){
                TaskDetails::where('task_id',$task->id)->delete();
                foreach($request->details as $key => $details){
                    $taskdetails = new TaskDetails();
                    $taskdetails->task_id = $task->id;
                    $taskdetails->name = $details['name'];
                    $taskdetails->description = $details['description'];
                    $taskdetails->save();
                }
            }
            return $this->successResponse($task,'Task has been Update Successfully', Response::HTTP_OK);
        }
        catch(Exception $e){
            return response()->json(['error'=>1, 'message'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $task = Task::where('id',$id)->first();
            if(!$task){
                return response()->json(['error'=>1, 'message'=>'Task not found']);    
            }
            $task = Task::where('id',$id)->delete();
            return $this->successResponse($task,'Task has been deleted successfully', Response::HTTP_OK);
        }
        catch(Exception $e){
            return response()->json(['error'=>1, 'message'=>$e->getMessage()]);
        }
    }
}
