<?php

namespace Tests\Unit\Components\Branch;

use Tests\TestCase;
use App\Models\Branch;
use App\Components\Branch\BranchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected BranchRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BranchRepository();
    }

    /** @test */
    public function it_can_create_a_branch()
    {
        $data = [
            'name' => 'Sucursal Central',
            'code' => 'BR-001',
            'is_active' => true
        ];

        $branch = $this->repository->create($data);

        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertDatabaseHas('branches', ['code' => 'BR-001']);
    }

    /** @test */
    public function it_can_update_a_branch()
    {
        $branch = Branch::factory()->create(['name' => 'Nombre Viejo']);
        
        $updated = $this->repository->update($branch, ['name' => 'Nombre Nuevo']);

        $this->assertEquals('Nombre Nuevo', $updated->name);
        $this->assertDatabaseHas('branches', ['name' => 'Nombre Nuevo']);
    }

    /** @test */
    public function it_can_delete_a_branch()
    {
        $branch = Branch::factory()->create();

        $this->repository->delete($branch);

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }
}