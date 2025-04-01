<?php

namespace App\Modules\Links\Factories;

use App\Modules\Links\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class LinkFactory extends Factory
{
    protected $model = Link::class;

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
            'origin' => fake()->url(),
            'referral' => fake()->unique()->word()
        ];
    }

    public function group($id)
    {
        return $this->afterCreating(function (Link $link) use ($id) {
            $link->groups()->attach($id);
        });
    }

    public function user($id)
    {
        return $this->afterCreating(function (Link $link) use ($id) {
            $link->users()->attach($id);
        });
    }
}
