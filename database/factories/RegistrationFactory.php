<?php

namespace Database\Factories;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Registration>
 */
class RegistrationFactory extends Factory
{
    protected $model = Registration::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'mobilenumber' => $this->faker->numerify('##########'),
            'parent_name' => $this->faker->name,
            'relationship' => $this->faker->randomElement(['Father', 'Mother', 'Guardian']),
            'mother_tounge' => $this->faker->word,
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address,
            'state' => $this->faker->state,
            'district' => $this->faker->city,
            'pincode' => $this->faker->postcode,
            'photo' => 'photos/default.png',
            'birth_certificate' => 'certificates/default.png',
            'title' => $this->faker->word,
            'fide_id' => $this->faker->randomNumber(6),
            'aicp_id' => $this->faker->randomNumber(6),
            'player_type' => $this->faker->randomElement(['Junior', 'Senior']),
            'dob_registration' =>  $this->faker->date(),
        ];
    }
}
