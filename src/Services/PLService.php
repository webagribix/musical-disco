<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Batch;
use App\Models\Expense;
use App\Models\Sale;

class PLService
{
    public function __construct(
        private LiveCountService $liveCountService,
        private FCRService $fcrService
    ) {}

    public function compute(int $batchId): array
    {
        $batch = Batch::find($batchId);
        if (!$batch) return [];

        $totalRevenue  = Sale::totalRevenueForBatch($batchId);
        $totalExpenses = Expense::totalForBatch($batchId);
        $netProfit     = $totalRevenue - $totalExpenses;

        $revenueByType     = Sale::revenueByType($batchId);
        $expenseByCategory = Expense::byCategory($batchId);

        // Build keyed maps for view convenience
        $revenueKeyed = [];
        foreach ($revenueByType as $row) {
            $revenueKeyed[$row['sale_type']] = (float) $row['revenue'];
        }
        $expensesKeyed = [];
        foreach ($expenseByCategory as $row) {
            $expensesKeyed[$row['category']] = (float) $row['total'];
        }

        $liveCount = $this->liveCountService->getLiveCount($batchId);
        $fcr       = $this->fcrService->calculate($batchId);

        $totalEggsStmt = \App\Core\DB::getInstance()->query(
            'SELECT COALESCE(SUM(good_eggs),0) FROM egg_collection_logs WHERE batch_id = ?',
            [$batchId]
        );
        $totalEggs = (int) $totalEggsStmt->fetchColumn();

        $costPerBird = $liveCount > 0 ? $totalExpenses / $liveCount : 0.0;
        $costPerEgg  = $totalEggs  > 0 ? $totalExpenses / $totalEggs  : 0.0;

        return [
            'batch_id'            => $batchId,
            'batch_name'          => $batch['batch_name'],
            'total_revenue'       => round($totalRevenue, 2),
            'total_expenses'      => round($totalExpenses, 2),
            'net_profit'          => round($netProfit, 2),
            'revenue'             => $revenueKeyed,
            'expenses'            => $expensesKeyed,
            'revenue_by_type'     => $revenueByType,
            'expenses_by_category'=> $expenseByCategory,
            'live_count'          => $liveCount,
            'cost_per_bird'       => round($costPerBird, 2),
            'cost_per_egg'        => round($costPerEgg, 2),
            'fcr'                 => $fcr,
            'total_eggs'          => $totalEggs,
        ];
    }
}
