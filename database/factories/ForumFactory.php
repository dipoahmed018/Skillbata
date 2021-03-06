<?php

namespace Database\Factories;

use App\Models\Forum;
use App\Models\Tuition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Forum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    // public function configure(){
        
           // return $this->afterCreating();
    // }

    public function definition()
    {
        
        return [
            'name' => $this->faker->firstName('male'),
            'description' => $this->faker->paragraph(2),
        ];
    }
}
