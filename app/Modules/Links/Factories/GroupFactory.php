<?php

namespace App\Modules\Links\Factories;

use App\Modules\Links\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->domainWord(),
            'description' => fake()->text(),
        ];
    }

    public function user($id)
    {
        return $this->afterCreating(function (Group $group) use ($id) {
            $group->users()->attach($id);
        });
    }
}
