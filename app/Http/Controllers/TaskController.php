<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TasksResource;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TasksResource::collection(Task::where('user_id', Auth::user()->id)->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        // validate
        $request->validated($request->safe()->all());
        // create task
        try {
            $task = Task::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority
            ]);
        } catch (\Throwable $th) {
           return $this->error([
            'error' => $th,
           ], 'Can\'t create task', 401);
        }

        // response
        if($task)
        {
            return $this->success([
                'task' => new TasksResource($task)
            ], 'Task created Successfully');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        // check authorization for incoming user request
        return
        $this->isNotAuthorized($task)
        ?
        $this->isNotAuthorized($task)
        :
        $this->success([
            'task' => new TasksResource($task)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if($this->isNotAuthorized($task))
        {
            return $this->isNotAuthorized($task);
        }
        else
        {
            $request->validated($request->safe()->all());

            $task->update($request->safe()->all());

            return $this->success([
                'task' => new TasksResource($task)
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if($this->isNotAuthorized($task))
        {
            return $this->isNotAuthorized($task);
        }
        else
        {
            try {
                $task->delete();
                return $this->success(null,'Task Deleted Successfully', 204);
            } catch (\Throwable $th) {
                return $this->error('', 'Resource Not Found '. $th, 404);
            }
        }
    }

    private function isNotAuthorized($task)
    {
        if(Auth::user()->id !== $task->user_id)
        {
            return $this->error('', 'Unauthorized request', 403);
        }
    }
}
