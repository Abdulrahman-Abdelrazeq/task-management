<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending'],
            ['name' => 'In_progress'],
            ['name' => 'Completed'],
        ];

        foreach ($statuses as $status) {
            Status::upsert($status, ['name']);
        }
    }
}
