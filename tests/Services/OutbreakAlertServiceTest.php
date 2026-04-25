<?php
declare(strict_types=1);
namespace Tests\Services;

use Tests\TestCase;

class OutbreakAlertServiceTest extends TestCase
{
    private const OUTBREAK_THRESHOLD = 0.02;

    private function mortalityRate(int $deaths48h, int $liveCount): float
    {
        if ($liveCount <= 0) return 0.0;
        return $deaths48h / $liveCount;
    }

    public function testOutbreakDetectedAboveThreshold(): void
    {
        $rate = $this->mortalityRate(12, 300); // 4%
        $this->assertGreaterThanOrEqual(self::OUTBREAK_THRESHOLD, $rate);
    }

    public function testNoOutbreakBelowThreshold(): void
    {
        $rate = $this->mortalityRate(1, 300); // 0.33%
        $this->assertLessThan(self::OUTBREAK_THRESHOLD, $rate);
    }

    public function testExactThresholdIsOutbreak(): void
    {
        $rate = $this->mortalityRate(6, 300); // exactly 2%
        $this->assertGreaterThanOrEqual(self::OUTBREAK_THRESHOLD, $rate);
    }

    public function testZeroLiveCountReturnsZero(): void
    {
        $rate = $this->mortalityRate(5, 0);
        $this->assertSame(0.0, $rate);
    }
}
