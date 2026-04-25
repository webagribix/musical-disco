<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Batch;
use App\Models\Breed;
use App\Models\VaccinationSchedule;

class VaccinationScheduleService
{
    /** Default vaccination day offsets from placement date */
    private const DEFAULT_SCHEDULE = [
        ['day' => 1,  'vaccine' => 'Newcastle (Live)', 'route' => 'eye_drop'],
        ['day' => 7,  'vaccine' => 'Gumboro (IBD)',    'route' => 'drinking_water'],
        ['day' => 14, 'vaccine' => 'Newcastle (Live)', 'route' => 'drinking_water'],
        ['day' => 21, 'vaccine' => 'Gumboro (IBD) 2nd','route' => 'drinking_water'],
        ['day' => 28, 'vaccine' => 'Newcastle (Killed)','route' => 'injection'],
        ['day' => 42, 'vaccine' => 'Newcastle Booster', 'route' => 'drinking_water'],
        ['day' => 56, 'vaccine' => 'Fowl Typhoid',      'route' => 'injection'],
    ];

    public function generateForBatch(int $batchId): void
    {
        $batch = Batch::find($batchId);
        if (!$batch) return;

        $placementDate = new \DateTimeImmutable($batch['placement_date']);

        // Check if breed has a custom template
        $schedule = static::DEFAULT_SCHEDULE;

        foreach ($schedule as $item) {
            $dueDate = $placementDate->modify("+{$item['day']} days")->format('Y-m-d');
            VaccinationSchedule::create([
                'batch_id'    => $batchId,
                'vaccine_name'=> $item['vaccine'],
                'due_date'    => $dueDate,
                'route'       => $item['route'],
                'age_days'    => $item['day'],
                'status'      => 'pending',
            ]);
        }
    }

    public function getPendingForBatch(int $batchId): array
    {
        return \App\Core\DB::getInstance()->selectWhere(
            'vaccination_schedules',
            'batch_id = ? AND status = ? AND due_date <= ?',
            [$batchId, 'pending', date('Y-m-d')]
        );
    }

    public function getUpcomingForBatch(int $batchId, int $days = 7): array
    {
        $until = date('Y-m-d', strtotime("+$days days"));
        return \App\Core\DB::getInstance()->selectWhere(
            'vaccination_schedules',
            'batch_id = ? AND status = ? AND due_date BETWEEN ? AND ?',
            [$batchId, 'pending', date('Y-m-d'), $until],
            ['order_by' => 'due_date ASC']
        );
    }
}
