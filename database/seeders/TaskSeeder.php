<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Task::create([
    'title' => 'Review Sales Order',
    'assigned_to' => 1,
    'status' => 'pending',
    'due_date' => now()->addDays(2),
]);
    }
}
