<?php
declare(strict_types=1);
namespace App\Jobs;
use App\Core\DB;
use App\Models\{Batch,Task};

class TaskGeneratorJob
{
    private const DAILY_TASKS = ['feeding','egg_collection','water_check','weight_check'];

    public function handle(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $batches  = DB::getInstance()->selectWhere('batches', 'deleted_at IS NULL AND status = ?', ['active']);

        foreach ($batches as $batch) {
            foreach (static::DAILY_TASKS as $type) {
                $existing = DB::getInstance()->selectWhere(
                    'tasks',
                    'batch_id = ? AND task_type = ? AND due_date = ?',
                    [(int)$batch['id'], $type, $tomorrow]
                );
                if (!$existing) {
                    Task::create([
                        'batch_id'  => $batch['id'],
                        'task_type' => $type,
                        'due_date'  => $tomorrow,
                        'status'    => 'pending',
                    ]);
                }
            }
        }
    }
}
