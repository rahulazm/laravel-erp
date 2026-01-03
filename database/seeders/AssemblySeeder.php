<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assembly;
use App\Models\Category;

class AssemblySeeder extends Seeder
{
    public function run(): void
    {
        // Create categories if not exists
        $categories = [
            'Electronics' => [
                'PCB Assembly',
                'Cables & Connectors',
                'Sensors',
                'Power Supplies',
                'Control Units'
            ],
            'Mechanical' => [
                'Metal Parts',
                'Plastic Parts',
                'Fasteners',
                'Bearings',
                'Gears'
            ],
            'Hydraulic' => [
                'Pumps',
                'Valves',
                'Cylinders',
                'Hoses',
                'Fittings'
            ],
            'Pneumatic' => [
                'Compressors',
                'Valves',
                'Cylinders',
                'Fittings',
                'Regulators'
            ],
            'Electrical' => [
                'Motors',
                'Switches',
                'Relays',
                'Transformers',
                'Wiring'
            ]
        ];

        $categoryMap = [];
        foreach ($categories as $parent => $children) {
            $parentCat = Category::firstOrCreate(
                ['name' => $parent],
                ['type' => 'assembly', 'description' => $parent . ' Components']
            );
            
            foreach ($children as $child) {
                $category = Category::firstOrCreate(
                    ['name' => $child],
                    [
                        'parent_id' => $parentCat->id,
                        'type' => 'assembly',
                        'description' => $child . ' for assemblies'
                    ]
                );
                $categoryMap[$child] = $category->id;
            }
        }

        // Create sample assemblies
        $assemblies = [
            // Final Products
            [
                'code' => 'ASM-1001',
                'name' => 'Industrial Control Panel',
                'type' => 'product',
                'category_id' => $categoryMap['Control Units'],
                'unit_of_measure' => 'pcs',
                'standard_cost' => 1250.50,
                'selling_price' => 1899.99,
                'current_stock' => 15,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'manufacturing_lead_time' => 10,
                'weight' => 25.5,
                'is_active' => true,
                'is_sellable' => true,
                'is_manufacturable' => true,
            ],
            [
                'code' => 'ASM-1002',
                'name' => 'Hydraulic Power Unit',
                'type' => 'product',
                'category_id' => $categoryMap['Pumps'],
                'unit_of_measure' => 'pcs',
                'standard_cost' => 3200.75,
                'selling_price' => 4999.99,
                'current_stock' => 8,
                'minimum_stock' => 3,
                'maximum_stock' => 20,
                'manufacturing_lead_time' => 15,
                'weight' => 85.2,
                'is_active' => true,
                'is_sellable' => true,
                'is_manufacturable' => true,
            ],
            
            // Sub-Assemblies
            [
                'code' => 'ASM-2001',
                'name' => 'Control Board Assembly',
                'type' => 'sub_assembly',
                'category_id' => $categoryMap['PCB Assembly'],
                'unit_of_measure' => 'pcs',
                'standard_cost' => 185.25,
                'current_stock' => 45,
                'minimum_stock' => 20,
                'manufacturing_lead_time' => 3,
                'weight' => 1.2,
                'is_active' => true,
                'is_manufacturable' => true,
            ],
            [
                'code' => 'ASM-2002',
                'name' => 'Hydraulic Valve Block',
                'type' => 'sub_assembly',
                'category_id' => $categoryMap['Valves'],
                'unit_of_measure' => 'pcs',
                'standard_cost' => 425.80,
                'current_stock' => 22,
                'minimum_stock' => 10,
                'manufacturing_lead_time' => 5,
                'weight' => 12.5,
                'is_active' => true,
                'is_manufacturable' => true,
            ],
            
            // Components
            [
                'code' => 'ASM-3001',
                'name' => '24V DC Relay',
                'type' => 'component',
                'category_id' => $categoryMap['Relays'],
                'unit_of_measure' => 'pcs',
                'purchase_price' => 8.95,
                'standard_cost' => 9.50,
                'current_stock' => 250,
                'minimum_stock' => 100,
                'maximum_stock' => 500,
                'purchase_lead_time' => 7,
                'weight' => 0.05,
                'is_active' => true,
                'is_purchasable' => true,
                'tracking_method' => 'batch',
            ],
            [
                'code' => 'ASM-3002',
                'name' => 'M12x1.5 Bolt',
                'type' => 'component',
                'category_id' => $categoryMap['Fasteners'],
                'unit_of_measure' => 'pcs',
                'purchase_price' => 0.85,
                'standard_cost' => 0.95,
                'current_stock' => 1500,
                'minimum_stock' => 500,
                'maximum_stock' => 3000,
                'purchase_lead_time' => 5,
                'weight' => 0.02,
                'is_active' => true,
                'is_purchasable' => true,
            ],
            
            // Raw Materials
            [
                'code' => 'ASM-4001',
                'name' => 'Steel Sheet 3mm',
                'type' => 'raw_material',
                'category_id' => $categoryMap['Metal Parts'],
                'unit_of_measure' => 'sqm',
                'purchase_price' => 45.75,
                'standard_cost' => 48.25,
                'current_stock' => 125.5,
                'minimum_stock' => 50,
                'maximum_stock' => 200,
                'purchase_lead_time' => 14,
                'weight' => 23.55,
                'is_active' => true,
                'is_purchasable' => true,
                'tracking_method' => 'serial',
            ],
            [
                'code' => 'ASM-4002',
                'name' => 'PVC Insulation',
                'type' => 'raw_material',
                'category_id' => $categoryMap['Wiring'],
                'unit_of_measure' => 'meter',
                'purchase_price' => 2.25,
                'standard_cost' => 2.50,
                'current_stock' => 500,
                'minimum_stock' => 200,
                'maximum_stock' => 1000,
                'purchase_lead_time' => 10,
                'weight' => 0.15,
                'is_active' => true,
                'is_purchasable' => true,
            ],
        ];

        foreach ($assemblies as $assemblyData) {
            Assembly::firstOrCreate(
                ['code' => $assemblyData['code']],
                $assemblyData
            );
        }

        // Create sample BOM relationships
        $controlPanel = Assembly::where('code', 'ASM-1001')->first();
        $controlBoard = Assembly::where('code', 'ASM-2001')->first();
        $relay = Assembly::where('code', 'ASM-3001')->first();

        if ($controlPanel && $controlBoard) {
            $controlPanel->components()->firstOrCreate([
                'component_id' => $controlBoard->id,
            ], [
                'quantity' => 1,
                'unit' => 'pcs',
                'position' => 1,
                'operation' => 'PCB Assembly',
                'unit_cost' => $controlBoard->standard_cost,
            ]);
        }

        if ($controlBoard && $relay) {
            $controlBoard->components()->firstOrCreate([
                'component_id' => $relay->id,
            ], [
                'quantity' => 4,
                'unit' => 'pcs',
                'position' => 1,
                'operation' => 'SMD Placement',
                'unit_cost' => $relay->standard_cost,
            ]);
        }

        $this->command->info('Assemblies and BOM structures seeded successfully!');
    }
}