<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Submission::factory()->count(5)->create();

        Submission::factory()->count(3)->completed()->create();

        Submission::factory()->count(3)->failed()->create();
    }
}
