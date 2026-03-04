<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use App\Models\MaintenanceSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        $statuses = ['pending', 'in_progress', 'completed', 'verified', 'cancelled'];
        $status = fake()->randomElement($statuses);
        $types = ['preventive', 'corrective', 'emergency', 'inspection'];
        
        // Get existing users first
        $technician = $this->getTechnicianUser();
        $supervisor = $this->getSupervisorUser();

        // Generate dates with simple approach
        $scheduledDate = $this->getRandomDate('-1 month', '+1 month');
        
        // Initialize dates based on status
        $dates = $this->generateDatesForStatus($status, $scheduledDate);

        return [
            'work_order_number' => fake()->unique()->regexify('WO-[0-9]{8}-[0-9]{4}'),
            'asset_id' => $this->getRandomAssetId(),
            'maintenance_schedule_id' => $this->getRandomScheduleId(),
            'technician_id' => $technician->id,
            'supervisor_id' => $supervisor->id,
            'type' => fake()->randomElement($types),
            'status' => $status,
            'title' => fake()->randomElement(['Routine', 'Emergency', 'Scheduled', 'Corrective']) . ' Maintenance: ' . fake()->words(3, true),
            'description' => fake()->paragraphs(3, true),
            'checklist' => json_encode([
                ['task' => 'Visual inspection', 'required' => true],
                ['task' => 'Check connections', 'required' => true],
                ['task' => 'Measure parameters', 'required' => true],
                ['task' => 'Test operation', 'required' => true],
                ['task' => 'Clean equipment', 'required' => false],
            ]),
            'checklist_responses' => $status !== 'pending' ? json_encode($this->generateChecklistResponses()) : null,
            'scheduled_date' => $scheduledDate,
            'started_at' => $dates['started_at'],
            'completed_date' => $dates['completed_date'],
            'verified_at' => $dates['verified_at'],
            'time_spent_minutes' => in_array($status, ['completed', 'verified']) ? fake()->numberBetween(30, 480) : null,
            'parts_used' => in_array($status, ['completed', 'verified']) ? json_encode($this->generatePartsUsed()) : null,
            'technician_remarks' => in_array($status, ['completed', 'verified']) ? fake()->optional(0.7)->sentence() : null,
            'supervisor_remarks' => $status === 'verified' ? fake()->optional(0.5)->sentence() : null,
            'created_at' => $scheduledDate,
            'updated_at' => $dates['verified_at'] ?? $dates['completed_date'] ?? $dates['started_at'] ?? $scheduledDate,
        ];
    }

    /**
     * Get a technician user
     */
    private function getTechnicianUser()
    {
        $technician = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->inRandomOrder()->first();

        if (!$technician) {
            $technician = User::factory()->technician()->create();
        }

        return $technician;
    }

    /**
     * Get a supervisor user
     */
    private function getSupervisorUser()
    {
        $supervisor = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'maintenance-supervisor']);
        })->inRandomOrder()->first();

        if (!$supervisor) {
            $supervisor = User::factory()->supervisor()->create();
        }

        return $supervisor;
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
     * Get a random schedule ID
     */
    private function getRandomScheduleId()
    {
        if (fake()->boolean(40)) {
            $schedule = MaintenanceSchedule::inRandomOrder()->first();
            return $schedule ? $schedule->id : null;
        }
        return null;
    }

    /**
     * Generate dates based on status
     */
    private function generateDatesForStatus($status, $scheduledDate)
    {
        $dates = [
            'started_at' => null,
            'completed_date' => null,
            'verified_at' => null
        ];

        $now = new \DateTime();

        switch ($status) {
            case 'in_progress':
                // Ensure scheduled date is in the past
                if ($scheduledDate >= $now) {
                    $scheduledDate = (clone $now)->modify('-2 days');
                }
                $dates['started_at'] = $this->getRandomDateBetween($scheduledDate, $now);
                break;

            case 'completed':
                // Ensure scheduled date is well in the past
                if ($scheduledDate >= $now) {
                    $scheduledDate = (clone $now)->modify('-10 days');
                }
                $startedAt = $this->getRandomDateBetween($scheduledDate, (clone $now)->modify('-2 days'));
                $dates['started_at'] = $startedAt;
                $dates['completed_date'] = $this->getRandomDateBetween($startedAt, $now);
                break;

            case 'verified':
                // Ensure all dates are properly sequenced
                if ($scheduledDate >= $now) {
                    $scheduledDate = (clone $now)->modify('-15 days');
                }
                $startedAt = $this->getRandomDateBetween($scheduledDate, (clone $now)->modify('-5 days'));
                $completedDate = $this->getRandomDateBetween($startedAt, (clone $now)->modify('-2 days'));
                $dates['started_at'] = $startedAt;
                $dates['completed_date'] = $completedDate;
                $dates['verified_at'] = $this->getRandomDateBetween($completedDate, $now);
                break;
        }

        return $dates;
    }

    /**
     * Get a random date between two dates
     */
    private function getRandomDateBetween($startDate, $endDate)
    {
        if ($startDate >= $endDate) {
            return clone $endDate;
        }
        
        $timestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    /**
     * Get a random date in range
     */
    private function getRandomDate($start, $end)
    {
        $timestamp = mt_rand(
            strtotime($start),
            strtotime($end)
        );
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    /**
     * Generate checklist responses
     */
    private function generateChecklistResponses()
    {
        return [
            ['task' => 'Visual inspection', 'completed' => fake()->boolean(90)],
            ['task' => 'Check connections', 'completed' => fake()->boolean(90)],
            ['task' => 'Measure parameters', 'completed' => fake()->boolean(90)],
            ['task' => 'Test operation', 'completed' => fake()->boolean(90)],
            ['task' => 'Clean equipment', 'completed' => fake()->boolean(70)],
        ];
    }

    /**
     * Generate parts used
     */
    private function generatePartsUsed()
    {
        $parts = [
            ['name' => 'Bearing 6304', 'quantity' => 2, 'part_number' => 'BRG-6304'],
            ['name' => 'Oil Seal', 'quantity' => 1, 'part_number' => 'SL-45-60'],
            ['name' => 'Grease', 'quantity' => 1, 'part_number' => 'GRS-LG2'],
            ['name' => 'Filter', 'quantity' => 1, 'part_number' => 'FIL-200'],
            ['name' => 'Capacitor', 'quantity' => 3, 'part_number' => 'CAP-450uF'],
            ['name' => 'Contactor', 'quantity' => 1, 'part_number' => 'CTC-9A'],
        ];

        return fake()->randomElements($parts, fake()->numberBetween(0, 3));
    }

    // State methods remain the same but simplified
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'started_at' => null,
            'completed_date' => null,
            'verified_at' => null,
            'time_spent_minutes' => null,
            'parts_used' => null,
            'technician_remarks' => null,
            'supervisor_remarks' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $scheduledDate = $attributes['scheduled_date'] ?? $this->getRandomDate('-1 month', 'now');
            $now = new \DateTime();
            
            if ($scheduledDate >= $now) {
                $scheduledDate = (clone $now)->modify('-2 days');
            }
            
            return [
                'status' => 'in_progress',
                'started_at' => $this->getRandomDateBetween($scheduledDate, $now),
                'completed_date' => null,
                'verified_at' => null,
                'time_spent_minutes' => null,
                'parts_used' => null,
                'supervisor_remarks' => null,
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $scheduledDate = $attributes['scheduled_date'] ?? $this->getRandomDate('-2 months', '-1 week');
            $now = new \DateTime();
            
            $startedAt = $this->getRandomDateBetween($scheduledDate, (clone $now)->modify('-2 days'));
            $completedDate = $this->getRandomDateBetween($startedAt, $now);
            
            return [
                'status' => 'completed',
                'started_at' => $startedAt,
                'completed_date' => $completedDate,
                'verified_at' => null,
                'time_spent_minutes' => fake()->numberBetween(30, 480),
                'parts_used' => json_encode($this->generatePartsUsed()),
                'technician_remarks' => fake()->optional(0.8)->sentence(),
            ];
        });
    }

    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            $scheduledDate = $attributes['scheduled_date'] ?? $this->getRandomDate('-3 months', '-2 weeks');
            $now = new \DateTime();
            
            $startedAt = $this->getRandomDateBetween($scheduledDate, (clone $now)->modify('-5 days'));
            $completedDate = $this->getRandomDateBetween($startedAt, (clone $now)->modify('-2 days'));
            $verifiedAt = $this->getRandomDateBetween($completedDate, $now);
            
            return [
                'status' => 'verified',
                'started_at' => $startedAt,
                'completed_date' => $completedDate,
                'verified_at' => $verifiedAt,
                'time_spent_minutes' => fake()->numberBetween(30, 480),
                'parts_used' => json_encode($this->generatePartsUsed()),
                'technician_remarks' => fake()->sentence(),
                'supervisor_remarks' => fake()->optional(0.7)->sentence(),
            ];
        });
    }

    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'emergency',
            'title' => 'EMERGENCY: ' . fake()->sentence(4),
            'scheduled_date' => $this->getRandomDate('-2 days', 'now'),
        ]);
    }

    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'preventive',
        ]);
    }
}