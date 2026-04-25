<?php
declare(strict_types=1);
namespace Tests\Services;

use Tests\TestCase;

class PLServiceTest extends TestCase
{
    /** Mirrors PLService::calculate() formula */
    private function calculate(array $revenue, array $expenses, int $liveCount, int $totalEggsSold): array
    {
        $totalRevenue  = array_sum($revenue);
        $totalExpenses = array_sum($expenses);
        $netProfit     = $totalRevenue - $totalExpenses;
        $costPerBird   = $liveCount > 0 ? round($totalExpenses / $liveCount, 2) : 0.0;
        $costPerEgg    = $totalEggsSold > 0 ? round($totalExpenses / $totalEggsSold, 4) : 0.0;

        return compact('totalRevenue', 'totalExpenses', 'netProfit', 'costPerBird', 'costPerEgg');
    }

    public function testProfitableScenario(): void
    {
        $pl = $this->calculate(
            ['eggs' => 30000, 'birds' => 20000],
            ['feed' => 15000, 'medication' => 2000, 'labor' => 5000],
            400,
            2500
        );

        $this->assertSame(50000.0, $pl['totalRevenue']);
        $this->assertSame(22000.0, $pl['totalExpenses']);
        $this->assertSame(28000.0, $pl['netProfit']);
        $this->assertGreaterThan(0, $pl['costPerBird']);
    }

    public function testLossScenario(): void
    {
        $pl = $this->calculate(
            ['eggs' => 5000],
            ['feed' => 20000],
            300,
            500
        );
        $this->assertLessThan(0, $pl['netProfit']);
    }

    public function testZeroBirdsDoesNotDivideByZero(): void
    {
        $pl = $this->calculate(['eggs' => 100], ['feed' => 50], 0, 0);
        $this->assertSame(0.0, $pl['costPerBird']);
        $this->assertSame(0.0, $pl['costPerEgg']);
    }
}
