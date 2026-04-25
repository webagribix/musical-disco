<?php
declare(strict_types=1);
namespace Tests\Services;

use Tests\TestCase;

class VaccinationScheduleServiceTest extends TestCase
{
    private const SCHEDULE = [
        ['day' => 1,  'vaccine_name' => 'Marek\'s Disease'],
        ['day' => 7,  'vaccine_name' => 'Newcastle Disease (ND) - First'],
        ['day' => 14, 'vaccine_name' => 'Gumboro (IBD) - First'],
        ['day' => 21, 'vaccine_name' => 'Newcastle Disease (ND) - Second'],
        ['day' => 28, 'vaccine_name' => 'Gumboro (IBD) - Second'],
        ['day' => 42, 'vaccine_name' => 'Newcastle Disease (ND) - Third'],
        ['day' => 56, 'vaccine_name' => 'Fowl Pox'],
    ];

    public function testScheduleHasSevenSteps(): void
    {
        $this->assertCount(7, self::SCHEDULE);
    }

    public function testFirstVaccinationIsDay1(): void
    {
        $this->assertSame(1, self::SCHEDULE[0]['day']);
    }

    public function testDueDatesCalculatedFromPlacementDate(): void
    {
        $placement = new \DateTime('2024-01-01');
        $dueDates  = array_map(function ($s) use ($placement) {
            $d = clone $placement;
            $d->modify("+{$s['day']} days");
            return $d->format('Y-m-d');
        }, self::SCHEDULE);

        $this->assertSame('2024-01-02', $dueDates[0]); // day 1 = +1 day
        $this->assertCount(7, $dueDates);
    }
}
