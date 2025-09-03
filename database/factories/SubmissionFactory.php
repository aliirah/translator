<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'base_lang'   => 'en',
            'target_lang' => $this->faker->randomElement(['es', 'fr', 'de']),
            'status'      => SubmissionStatus::default()->value,
            'translated'  => null,
            'error'       => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'     => SubmissionStatus::Completed->value,
            'translated' => [
                'name'        => $this->faker->name(),
                'title'       => $this->faker->sentence(4),
                'description' => $this->faker->paragraph(),
            ],
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => SubmissionStatus::Failed->value,
            'error'  => 'Translation failed for testing purposes',
        ]);
    }
}
