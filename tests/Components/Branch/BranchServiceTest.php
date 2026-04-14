<?php

namespace Tests\Unit\Components\Branch;

use Tests\TestCase;
use App\Models\Branch;
use App\Components\Branch\BranchService;
use App\Components\Branch\BranchRepository;
use App\Components\Branch\DTO\CreateBranchDto as BranchDto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BranchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Inyectamos el repo manualmente para el test de unidad
        $this->service = new BranchService(new BranchRepository());
    }

    /** @test */
    public function it_creates_a_branch_via_service_using_dto()
    {
        $dto = BranchDto::from([
            'name' => 'Sucursal Norte',
            'code' => 'BR-99',
            'is_active' => true
        ]);

        $branch = $this->service->create($dto);

        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertEquals('BR-99', $branch->code);
    }

    /** @test */
    public function it_finds_a_branch_by_id()
    {
        $branch = Branch::factory()->create();

        $found = $this->service->find($branch->id);

        $this->assertNotNull($found);
        $this->assertEquals($branch->id, $found->id);
    }
}