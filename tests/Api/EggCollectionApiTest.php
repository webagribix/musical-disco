<?php
declare(strict_types=1);
namespace Tests\Api;

use Tests\TestCase;

class EggCollectionApiTest extends TestCase
{
    private static string $baseUrl;

    public static function setUpBeforeClass(): void
    {
        self::$baseUrl = getenv('APP_TEST_BASE_URL') ?: '';
    }

    private function skip(): void
    {
        if (empty(self::$baseUrl)) {
            $this->markTestSkipped('APP_TEST_BASE_URL not set — skipping HTTP integration tests');
        }
    }

    public function testEggCollectionEndpointRequiresAuth(): void
    {
        $this->skip();
        $ch = curl_init(self::$baseUrl . '/api/v1/egg-collections');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->assertSame(401, $status);
    }

    public function testPostEggCollectionValidation(): void
    {
        $this->skip();
        $ch = curl_init(self::$baseUrl . '/api/v1/egg-collections');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer invalid'],
            CURLOPT_POSTFIELDS     => json_encode([]),
            CURLOPT_TIMEOUT        => 10,
        ]);
        curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->assertContains($status, [401, 422]);
    }
}
