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
        # dapatkan daftar seluruh tugas milik user via service
        $tasks = $this->taskService->getAllTasks($request->query('status'));

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data'    => $tasks,
        ], 200);
    }

    public function store(StoreTaskRequest $request)
    {
        # buat tugas baru untuk user via service
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data'    => $task,
        ], 201);
    }

    public function show($id)
    {
        # cari detail tugas spesifik user via service
        $task = $this->taskService->getTaskById($id);

        # kembalikan respon error jika tugas tidak ditemukan
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
        # perbarui rincian tugas milik user via service
        $task = $this->taskService->updateTask($request->validated(), $id);

        # kembalikan respon error jika tugas yang akan diperbarui tidak ditemukan
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
        # hapus data tugas milik user via service
        $task = $this->taskService->deleteTask($id);

        # kembalikan respon error jika tugas yang akan dihapus tidak ditemukan
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
        # tandai status pengerjaan tugas user sebagai selesai via service
        $result = $this->taskService->markTaskAsComplete($id);

        # kembalikan respon error jika tugas tidak ada atau sudah selesai sebelumnya
        if (!$result) {
            return response()->json(['message' => 'Task not found or already completed'], 404);
        }

        return response()->json([
            'message' => 'Task completed successfully',
            'data'    => $result,
        ], 200);
    }
}
