<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

/**
 * Runs inside each new tenant's database after migrations.
 * Seeds sensible defaults every tenant needs from day one.
 */
class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSalesPipeline();
    }

    private function seedSalesPipeline(): void
    {
        /** @var Pipeline $pipeline */
        $pipeline = Pipeline::firstOrCreate(
            ['name' => 'Sales Pipeline'],
        );

        $stages = [
            ['name' => 'Lead',      'position' => 1],
            ['name' => 'Qualified', 'position' => 2],
            ['name' => 'Proposal',  'position' => 3],
            ['name' => 'Negotiation', 'position' => 4],
            ['name' => 'Won',       'position' => 5],
            ['name' => 'Lost',      'position' => 6],
        ];

        foreach ($stages as $stage) {
            PipelineStage::firstOrCreate(
                [
                    'pipeline_id' => $pipeline->id,
                    'name'        => $stage['name'],
                ],
                ['position' => $stage['position']]
            );
        }
    }
}
