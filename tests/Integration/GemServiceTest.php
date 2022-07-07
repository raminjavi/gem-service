<?php

namespace Tests\Integration;

use App\Services\GemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_gem_to_user()
    {
        $gemService = new GemService;
        $gemService->add(1, 5, 'gift');
        $userTotalGems = $gemService->add(1, 15, 'gift');

        $this->assertEquals(20, $userTotalGems);
    }

    public function test_spend_user_gems()
    {
        $gemService = new GemService;
        $gemService->add(1, 5, 'gift');
        $userTotalGemsAfterSpend = $gemService->spend(1, 3, 'buy an item');

        $this->assertEquals(2, $userTotalGemsAfterSpend);
    }

}
