<?php

namespace Database\Factories;

use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaultFactory extends Factory
{
    protected $model = Fault::class;

    public function definition(): array
    {
        $statuses = ['reported', 'investigating', 'in_progress', 'resolved', 'closed'];
        $status = $this->faker->randomElement($statuses);

        $downtimeStart = $this->faker->dateTimeBetween('-1 week', 'now');
        $downtimeEnd = null;
        $downtimeMinutes = null;

        if (in_array($status, ['resolved', 'closed'])) {
            $downtimeEnd = $this->faker->dateTimeBetween($downtimeStart, '+3 days');
            // Integer minutes (1 min to 7 days)
            $downtimeMinutes = $this->faker->numberBetween(1, 10080);
        }

        return [
            'fault_number' => 'FLT-' . $this->faker->unique()->numerify('########') . '-' . $this->faker->numerify('####'),
            'asset_id' => Asset::inRandomOrder()->first()?->id ?? Asset::factory(),
            'reported_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'assigned_to' => $this->randomTechnician($this->faker, $status),
            'fault_type' => $this->faker->randomElement(['trip','overload','short_circuit','earth_fault','overheating','mechanical','other']),
            'severity' => $this->faker->randomElement(['low','medium','high','critical']),
            'status' => $status,
            'description' => $this->faker->paragraph(2),
            'symptoms' => json_encode($this->faker->randomElements([
                'Unusual noise','Equipment not starting','Overheating','High vibration',
                'Error code display','Intermittent tripping','Burning smell','Low performance',
                'Automatic shutdown','Alarm triggered'
            ], rand(2,4))),
            'images' => null,
            'downtime_start' => $downtimeStart,
            'downtime_end' => $downtimeEnd,
            'downtime_minutes' => $downtimeMinutes,
            'root_cause' => in_array($status, ['resolved','closed'])
                ? $this->faker->randomElement(['Bearing failure','Loose connection','Overload','Insulation failure','Voltage spike'])
                : null,
            'corrective_actions' => in_array($status, ['resolved','closed']) ? $this->faker->paragraph() : null,
            'parts_replaced' => in_array($status, ['resolved','closed']) ? json_encode($this->parts()) : null,
            'requires_followup' => $this->faker->boolean(20),
            'created_at' => $downtimeStart,
            'updated_at' => now(),
        ];
    }

    private function randomTechnician($faker, $status)
    {
        if (!in_array($status, ['investigating','in_progress','resolved','closed'])) return null;
        return User::whereHas('role', fn($q) => $q->where('slug', 'technician'))
            ->inRandomOrder()->first()?->id;
    }

    private function parts()
    {
        $parts = [
            ['name' => 'Bearing 6205', 'qty' => 2],
            ['name' => 'Contactor 9A', 'qty' => 1],
            ['name' => 'Fuse 10A', 'qty' => 3],
            ['name' => 'Capacitor 50uF', 'qty' => 1],
            ['name' => 'Sensor PT100', 'qty' => 1],
            ['name' => 'Cable 2.5mm²', 'qty' => 5],
        ];
        return $this->faker->randomElements($parts, rand(1,3));
    }

    // States...
    public function reported(): static { return $this->state(fn() => ['status' => 'reported']); }
    public function investigating(): static { return $this->state(fn() => ['status' => 'investigating']); }
    public function resolved(): static { return $this->state(fn() => ['status' => 'resolved']); }
    public function critical(): static { return $this->state(fn() => ['severity' => 'critical']); }
    public function high(): static { return $this->state(fn() => ['severity' => 'high']); }
    public function requiresFollowup(): static { return $this->state(fn() => ['requires_followup' => true]); }
}