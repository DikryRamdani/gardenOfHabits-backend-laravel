<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Tasks;
use App\Repositories\Contracts\TaskRepositoryInterface;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function getAllByUser(User $user, ?string $status = null)
    {
        $query = $user->tasks()->latest();

        # memfilter data tugas berdasarkan status pengerjaan jika diberikan
        if ($status === 'active') {
            $query->where('is_completed', false);
        } elseif ($status === 'completed') {
            $query->where('is_completed', true);
        }

        return $query->get();
    }

    public function createForUser(User $user, array $data): Tasks
    {
        # simpan data tugas baru yang dibuat oleh user
        return $user->tasks()->create($data);
    }

    public function findByUserAndId(User $user, $id): ?Tasks
    {
        # cari data tugas tertentu milik user berdasarkan id
        return $user->tasks()->find($id);
    }

    public function getDifficulty(User $user): ?Tasks
    {
        # dapatkan tugas pertama milik user yang belum diselesaikan
        return $user->tasks()->where('is_completed', false)->first();
    }

    public function updateTask(Tasks $task, array $data): Tasks
    {
        # perbarui data tugas di database
        $task->update($data);
        
        return $task->fresh();
    }

    public function deleteTask(Tasks $task): bool
    {
        # hapus data tugas dari database
        return (bool) $task->delete();
    }

    public function markCompleted(Tasks $task): Tasks
    {
        # tandai tugas sebagai selesai beserta pencatatan waktu penyelesaiannya
        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return $task->fresh();
    }
}
