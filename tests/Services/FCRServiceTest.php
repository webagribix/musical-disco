<?php
declare(strict_types=1);
namespace Tests\Services;

use Tests\TestCase;

class FCRServiceTest extends TestCase
{
    /**
     * FCR = totalFeed / ((currentWeight * liveCount) - (0.04 * initialCount))
     */
    private function computeFCR(float $totalFeed, float $currentWeight, int $liveCount, int $initialCount): float
    {
        $denominator = ($currentWeight * $liveCount) - (0.04 * $initialCount);
        if ($denominator <= 0) return 0.0;
        return round($totalFeed / $denominator, 2);
    }

    public function testFCRTypicalBroiler(): void
    {
        // 500 birds, avg weight 1.8kg, started with 500, fed 900kg total
        $fcr = $this->computeFCR(900, 1.8, 500, 500);
        $this->assertGreaterThan(0, $fcr);
        $this->assertLessThan(5, $fcr, 'FCR should be under 5 for good management');
    }

    public function testFCRZeroWhenDenominatorNegative(): void
    {
        $fcr = $this->computeFCR(100, 0.0, 0, 500);
        $this->assertSame(0.0, $fcr);
    }

    public function testFCRZeroWhenNoFeed(): void
    {
        $fcr = $this->computeFCR(0, 1.5, 400, 500);
        $this->assertSame(0.0, $fcr);
    }
}
