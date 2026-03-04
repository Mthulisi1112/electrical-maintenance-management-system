<?php

namespace Database\Factories;

use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class FaultFactory extends Factory
{
    protected $model = Fault::class;

    public function definition(): array
    {
        $faultTypes = ['trip', 'overload', 'short_circuit', 'earth_fault', 'overheating', 'mechanical', 'other'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['reported', 'investigating', 'in_progress', 'resolved', 'closed'];
        
        $status = fake()->randomElement($statuses);
        
        // Create DateTime objects for timestamps
        $downtimeStart = fake()->dateTimeBetween('-1 week', 'now');
        $downtimeEnd = null;
        $downtimeMinutes = null;
        
        if (in_array($status, ['resolved', 'closed'])) {
            $downtimeEnd = fake()->dateTimeBetween($downtimeStart, '+3 days');
            
            // Calculate minutes using DateTime diff
            $interval = $downtimeStart->diff($downtimeEnd);
            $downtimeMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        }
        
        $symptoms = [
            'Unusual noise from equipment',
            'Equipment not starting',
            'Overheating detected',
            'Vibration levels high',
            'Error code on display',
            'Tripping intermittently',
            'Smoke or burning smell',
            'Low output/performance',
            'Automatic shutdown',
            'Control system alarm'
        ];

        return [
            'fault_number' => fake()->unique()->regexify('FLT-[0-9]{8}-[0-9]{4}'),
            'asset_id' => $this->getRandomAssetId(),
            'reported_by' => $this->getRandomReporterId(),
            'assigned_to' => $this->getRandomTechnicianId($status),
            'fault_type' => fake()->randomElement($faultTypes),
            'severity' => fake()->randomElement($severities),
            'status' => $status,
            'description' => fake()->paragraphs(2, true),
            'symptoms' => json_encode(fake()->randomElements($symptoms, fake()->numberBetween(2, 4))),
            'images' => null,
            'downtime_start' => $downtimeStart,
            'downtime_end' => $downtimeEnd,
            'downtime_minutes' => $downtimeMinutes,
            'root_cause' => in_array($status, ['resolved', 'closed']) ? $this->getRandomRootCause() : null,
            'corrective_actions' => in_array($status, ['resolved', 'closed']) ? fake()->paragraph() : null,
            'parts_replaced' => in_array($status, ['resolved', 'closed']) ? json_encode($this->generatePartsReplaced()) : null,
            'requires_followup' => fake()->boolean(20),
            'created_at' => $downtimeStart,
            'updated_at' => $downtimeEnd ?? fake()->dateTimeBetween($downtimeStart, 'now'),
        ];
    }

    /**
     * Get a random asset ID
     */
    private function getRandomAssetId()
    {
        $asset = Asset::inRandomOrder()->first();
        return $asset ? $asset->id : Asset::factory()->create()->id;
    }

    /**
     * Get a random reporter ID
     */
    private function getRandomReporterId()
    {
        $reporter = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['technician', 'maintenance-supervisor']);
        })->inRandomOrder()->first();

        if (!$reporter) {
            $reporter = User::factory()->technician()->create();
        }

        return $reporter->id;
    }

    /**
     * Get a random technician ID for assignment
     */
    private function getRandomTechnicianId($status)
    {
        if (in_array($status, ['investigating', 'in_progress', 'resolved', 'closed']) && fake()->boolean(70)) {
            $technician = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->inRandomOrder()->first();

            if ($technician) {
                return $technician->id;
            }
        }

        return null;
    }

    /**
     * Get random root cause
     */
    private function getRandomRootCause()
    {
        $causes = [
            'Worn out bearing',
            'Loose connection',
            'Insulation failure',
            'Overload condition',
            'Phase imbalance',
            'Contactor failure',
            'Sensor malfunction',
            'Cooling system failure',
            'Voltage spike',
            'Mechanical jamming',
            'Software glitch',
            'Communication error',
            'Power supply issue',
            'Environmental factor',
            'Normal wear and tear'
        ];

        return fake()->randomElement($causes);
    }

    /**
     * Generate parts replaced
     */
    private function generatePartsReplaced()
    {
        $parts = [
            ['name' => 'Bearing 6205', 'quantity' => 2, 'part_number' => 'BRG-6205'],
            ['name' => 'Contactor 9A', 'quantity' => 1, 'part_number' => 'CTC-9A-230'],
            ['name' => 'Fuse 10A', 'quantity' => 3, 'part_number' => 'FUS-10A-Fast'],
            ['name' => 'Capacitor 50uF', 'quantity' => 1, 'part_number' => 'CAP-50uF-450V'],
            ['name' => 'Temperature Sensor', 'quantity' => 1, 'part_number' => 'SNS-PT100'],
            ['name' => 'Cable 2.5mm²', 'quantity' => 5, 'part_number' => 'CAB-2.5-RED'],
            ['name' => 'Terminal Block', 'quantity' => 4, 'part_number' => 'TB-4mm-12way'],
            ['name' => 'Relay 24V', 'quantity' => 2, 'part_number' => 'REL-24V-4PDT'],
            ['name' => 'PCB Board', 'quantity' => 1, 'part_number' => 'PCB-CNTRL-V2'],
            ['name' => 'Cooling Fan', 'quantity' => 1, 'part_number' => 'FAN-120mm-24V'],
        ];

        return fake()->randomElements($parts, fake()->numberBetween(0, 3));
    }

    /**
     * Indicate that the fault is reported.
     */
    public function reported(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reported',
            'assigned_to' => null,
            'downtime_end' => null,
            'downtime_minutes' => null,
            'root_cause' => null,
            'corrective_actions' => null,
            'parts_replaced' => null,
        ]);
    }

    /**
     * Indicate that the fault is being investigated.
     */
    public function investigating(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'investigating',
                'downtime_end' => null,
                'downtime_minutes' => null,
                'root_cause' => null,
                'corrective_actions' => null,
                'parts_replaced' => null,
            ];
        });
    }

    /**
     * Indicate that the fault is resolved.
     */
    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            $downtimeStart = $attributes['downtime_start'] ?? fake()->dateTimeBetween('-5 days', '-2 days');
            $downtimeEnd = fake()->dateTimeBetween($downtimeStart, 'now');
            
            // Calculate minutes
            $interval = $downtimeStart->diff($downtimeEnd);
            $downtimeMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            
            return [
                'status' => 'resolved',
                'downtime_end' => $downtimeEnd,
                'downtime_minutes' => $downtimeMinutes,
                'root_cause' => $this->getRandomRootCause(),
                'corrective_actions' => fake()->paragraph(),
                'parts_replaced' => json_encode($this->generatePartsReplaced()),
            ];
        });
    }

    /**
     * Indicate that the fault is critical.
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'critical',
        ]);
    }

    /**
     * Indicate that the fault is high severity.
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'high',
        ]);
    }

    /**
     * Indicate that the fault requires follow-up.
     */
    public function requiresFollowup(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_followup' => true,
        ]);
    }

    /**
     * Set the asset for the fault.
     */
    public function forAsset(Asset $asset): static
    {
        return $this->state(fn (array $attributes) => [
            'asset_id' => $asset->id,
        ]);
    }

    /**
     * Assign the fault to a technician.
     */
    public function assignedTo(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $technician->id,
            'status' => 'investigating',
        ]);
    }
}