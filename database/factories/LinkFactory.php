<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'=> User::factory(),
            'url'=>$this->faker->url,
            'slug'=> Str::random(6),
        ];
    }

    public function ofUser(User $user)
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
