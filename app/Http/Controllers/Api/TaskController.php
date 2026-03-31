<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\Task\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $tasks = $this->taskService->getAllTasks($request->query('status'));

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data'    => $tasks,
        ], 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data'    => $task,
        ], 201);
    }

    public function show($id)
    {
        $task = $this->taskService->getTaskById($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json([
            'message' => 'Task retrieved successfully',
            'data'    => $task,
        ], 200);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = $this->taskService->updateTask($request->validated(), $id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'data'    => $task,
        ], 200);
    }

    public function destroy($id)
    {
        $task = $this->taskService->deleteTask($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json([
            'message' => 'Task deleted successfully',
            'data'    => $task,
        ], 200);
    }

    public function complete($id)
    {
        $result = $this->taskService->markTaskAsComplete($id);

        if (!$result) {
            return response()->json(['message' => 'Task not found or already completed'], 404);
        }

        return response()->json([
            'message' => 'Task completed successfully',
            'data'    => $result,
        ], 200);
    }
}
