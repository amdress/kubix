<?php

namespace Tests\Components\Branch;

use App\Models\Branch;
use Tests\TestCase;

class BranchModelTest extends TestCase
{
    /** @test */
    public function it_casts_is_active_to_boolean()
    {
        $branch = new Branch(['is_active' => 1]);
        $this->assertTrue($branch->is_active);

        $branch->is_active = 0;
        $this->assertFalse($branch->is_active);
    }

    /** @test */
    public function it_casts_branding_to_array()
    {
        $branding = ['color' => 'blue', 'logo' => 'logo.png'];
        $branch = new Branch(['branding' => $branding]);

        $this->assertIsArray($branch->branding);
        $this->assertEquals('blue', $branch->branding['color']);
        $this->assertEquals('logo.png', $branch->branding['logo']);
    }

    /** @test */
    public function it_returns_true_if_branch_is_active()
    {
        $branch = new Branch(['is_active' => true]);
        $this->assertTrue($branch->isActive());
    }

    /** @test */
    public function it_returns_false_if_branch_is_not_active()
    {
        $branch = new Branch(['is_active' => false]);
        $this->assertFalse($branch->isActive());
    }
}
