<?php

namespace Tests\ApiTests;

use App\Helpers\DataboxHelper;
use App\Services\FacebookMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class FacebookMetricsServiceTest extends TestCase
{
    public function testHandleWithValidAccessToken(): void
    {
        // Mocking Http facade
        Http::fake([
            '*' => Http::response(['summary' => ['total_count' => 100]], 200),
        ]);

        // Mocking Request instance
        $request = $this->createMock(Request::class);
        $request->method('get')->willReturn('valid_access_token');

        // Mocking DataboxHelper
        $databoxHelper = $this->createMock(DataboxHelper::class);
        $databoxHelper->expects($this->once())->method('push')->willReturn(['message' => 'Success', 'id' => '123']);

        $facebookMetricsService = new FacebookMetricsService($request);

        $result = $facebookMetricsService->handle($databoxHelper);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(100, $result['total_friends_amount']);

        // Ensure that FacebookMetric was created
        $this->assertDatabaseHas('facebook_metrics', ['total_friends_amount' => 100]);
    }

    public function testHandleWithInvalidAccessToken(): void
    {
        // Mocking Http facade
        Http::fake([
            '*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        // Mocking Request instance
        $request = $this->createMock(Request::class);
        $request->method('get')->willReturn('invalid_access_token');

        // Mocking DataboxHelper
        $databoxHelper = $this->createMock(DataboxHelper::class);

        $facebookMetricsService = new FacebookMetricsService($request);

        $result = $facebookMetricsService->handle($databoxHelper);

        $this->assertIsArray($result);
        $this->assertEmpty($result);

        // Ensure that no FacebookMetric was created
        $this->assertDatabaseMissing('facebook_metrics', ['total_friends_amount' => 100]);
    }
}

