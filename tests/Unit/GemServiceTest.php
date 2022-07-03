<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GemService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_gem_to_user()
    {
        $userTotalGems = (new GemService)->add(1, 5, 'gift');
        $this->assertIsInt($userTotalGems);
    }

    public function test_spend_user_gems()
    {
        $gemService = new GemService;
        $gemService->add(1, 5, 'gift');
        $userTotalGemsAfterSpend = $gemService->spend(1, 3, 'buy an item');
        $this->assertIsInt($userTotalGemsAfterSpend);
    }

    public function test_calculate_user_gems()
    {
        $gemService = new GemService;
        $gemService->add(1, 15, 'gift');
        $totalGems = $gemService->calculateTotalGems(1);
        $this->assertIsInt($totalGems);
    }
}
