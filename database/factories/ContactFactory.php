<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Debtor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = ['Personal', 'Trabajo', 'Referencia', 'Familiar', 'Legal', 'Cobranza'];
        $tipos = ['Celular', 'Telefono_fijo', 'Email', 'Direccion', 'Otro'];

        // Algunas lógicas para generar datos más coherentes
        $tipo = $this->faker->randomElement($tipos);
        $detalle = '';
        if ($tipo === 'Celular' || $tipo === 'Telefono_fijo') {
            $detalle = $this->faker->phoneNumber();
        } elseif ($tipo === 'Email') {
            $detalle = $this->faker->unique()->safeEmail();
        } elseif ($tipo === 'Direccion') {
            $detalle = $this->faker->address();
        } else { // Otro
            $detalle = $this->faker->word();
        }

        $isValidated = $this->faker->boolean(70); // 70% de posibilidades de estar validado

        return [
            'debtor_id' => $this->faker->randomElement([1,2,3,4,5]),// Asigna un deudor existente o crea uno nuevo
            'categoria' => $this->faker->randomElement($categorias),
            'tipo' => $tipo,
            'detalle' => $detalle,
            'validacion' => $isValidated,
            'principal' => $this->faker->boolean(20), // 20% de posibilidades de ser principal (luego el seeder lo ajustará)
            'observacion' => $this->faker->boolean(40) ? $this->faker->sentence() : null, // 40% de posibilidades de tener observación
            'usuario_validacion_id' => $isValidated ? User::factory() : null,
            'fecha_validacion' => $isValidated ? $this->faker->dateTimeBetween('-6 months', 'now') : null,
        ];
    }
}