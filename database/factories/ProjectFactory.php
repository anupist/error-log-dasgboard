<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Ecommerce Production',
            'ERP System',
            'POS API',
            'Client Website',
            'Mobile Backend',
            'Admin Panel',
            'Payment Gateway',
            'Inventory Service',
        ]);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'api_url'     => $this->faker->url(),
            'api_token'   => null,
            'environment' => $this->faker->randomElement(['production', 'staging', 'development']),
            'status'      => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
            'notes'       => $this->faker->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
