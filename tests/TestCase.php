<?php
declare(strict_types=1);
namespace Tests;

use App\Core\DB;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static bool $migrated = false;

    protected function setUp(): void
    {
        parent::setUp();
        DB::reset();
    }

    protected function tearDown(): void
    {
        DB::reset();
        parent::tearDown();
    }
}
