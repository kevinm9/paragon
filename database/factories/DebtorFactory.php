<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debtor>
 */
class DebtorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Debtor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'identificacion' => $this->faker->unique()->numerify('##########'), // 10 dígitos
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'estado_civil' => $this->faker->randomElement(['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Union_libre']),
            'nacionalidad' => $this->faker->country(),
            'direccion_principal' => $this->faker->address(),
            'ciudad' => $this->faker->city(),
            'provincia' => $this->faker->state(),
            'email_principal' => $this->faker->unique()->safeEmail(),
            'telefono_principal' => $this->faker->phoneNumber(),
            'observacion' => $this->faker->boolean(30) ? $this->faker->sentence() : null, // 30% de posibilidades de tener observación
            'usuario_creacion_id' => \App\Models\User::factory(), // Asigna un usuario existente o crea uno nuevo
            'fecha_creacion' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'activo' => $this->faker->boolean(90), // 90% de posibilidades de estar activo
        ];
    }

    /**
     * Indicate that the debtor is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
