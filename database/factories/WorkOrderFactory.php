<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use App\Models\MaintenanceSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        $statuses = ['pending', 'in_progress', 'completed', 'verified', 'cancelled'];
        $types = ['preventive', 'corrective', 'emergency', 'inspection'];

        $status = $this->faker->randomElement($statuses);

        $technician = $this->getTechnicianUser();
        $supervisor = $this->getSupervisorUser();

        $scheduledDate = Carbon::instance(
            $this->faker->dateTimeBetween('-1 month', '+1 month')
        );

        $dates = $this->generateDatesForStatus($status, $scheduledDate);

        return [
            'work_order_number' => $this->faker->unique()->regexify('WO-[0-9]{8}-[0-9]{4}'),
            'asset_id' => $this->getRandomAssetId(),
            'maintenance_schedule_id' => $this->getRandomScheduleId(),

            'technician_id' => $technician?->id,
            'supervisor_id' => $supervisor?->id,

            'type' => $this->faker->randomElement($types),
            'status' => $status,

            'title' => $this->faker->randomElement(['Routine', 'Emergency', 'Scheduled', 'Corrective']) . ' Maintenance: ' . $this->faker->words(3, true),
            'description' => $this->faker->paragraphs(3, true),

            'checklist' => json_encode([
                ['task' => 'Visual inspection', 'required' => true],
                ['task' => 'Check connections', 'required' => true],
                ['task' => 'Measure parameters', 'required' => true],
                ['task' => 'Test operation', 'required' => true],
                ['task' => 'Clean equipment', 'required' => false],
            ]),

            'checklist_responses' =>
                $status !== 'pending'
                    ? json_encode($this->generateChecklistResponses())
                    : null,

            'scheduled_date' => $scheduledDate,

            'started_at' => $dates['started_at'],
            'completed_date' => $dates['completed_date'],
            'verified_at' => $dates['verified_at'],

            'time_spent_minutes' => in_array($status, ['completed', 'verified'])
                ? $this->faker->numberBetween(30, 480)
                : null,

            'parts_used' => in_array($status, ['completed', 'verified'])
                ? json_encode($this->generatePartsUsed())
                : null,

            'technician_remarks' => in_array($status, ['completed', 'verified'])
                ? $this->faker->optional(0.7)->sentence()
                : null,

            'supervisor_remarks' => $status === 'verified'
                ? $this->faker->optional(0.5)->sentence()
                : null,

            'created_at' => $scheduledDate,
            'updated_at' => now(),
        ];
    }

    private function getTechnicianUser()
    {
        return User::whereHas('role', fn($q) =>
            $q->where('slug', 'technician')
        )->inRandomOrder()->first()
        ?? User::factory()->technician()->create();
    }

    private function getSupervisorUser()
    {
        return User::whereHas('role', fn($q) =>
            $q->whereIn('slug', ['admin', 'maintenance-supervisor'])
        )->inRandomOrder()->first()
        ?? User::factory()->supervisor()->create();
    }

    private function getRandomAssetId()
    {
        return Asset::inRandomOrder()->first()?->id
            ?? Asset::factory()->create()->id;
    }

    private function getRandomScheduleId()
    {
        if (!$this->faker->boolean(40)) {
            return null;
        }

        return MaintenanceSchedule::inRandomOrder()->first()?->id;
    }

    private function generateDatesForStatus(string $status, Carbon $scheduledDate): array
    {
        return match ($status) {
            'pending' => [
                'started_at' => null,
                'completed_date' => null,
                'verified_at' => null,
            ],

            'in_progress' => [
                'started_at' => $scheduledDate->copy()->addHours(rand(1, 48)),
                'completed_date' => null,
                'verified_at' => null,
            ],

            'completed' => [
                'started_at' => $scheduledDate->copy()->addHours(rand(1, 24)),
                'completed_date' => $scheduledDate->copy()->addDays(rand(1, 3)),
                'verified_at' => null,
            ],

            'verified' => [
                'started_at' => $scheduledDate->copy()->addHours(rand(1, 24)),
                'completed_date' => $scheduledDate->copy()->addDays(rand(1, 3)),
                'verified_at' => $scheduledDate->copy()->addDays(rand(4, 7)),
            ],

            default => [
                'started_at' => null,
                'completed_date' => null,
                'verified_at' => null,
            ],
        };
    }

    private function generateChecklistResponses(): array
    {
        return [
            ['task' => 'Visual inspection', 'completed' => $this->faker->boolean(90)],
            ['task' => 'Check connections', 'completed' => $this->faker->boolean(90)],
            ['task' => 'Measure parameters', 'completed' => $this->faker->boolean(90)],
            ['task' => 'Test operation', 'completed' => $this->faker->boolean(90)],
            ['task' => 'Clean equipment', 'completed' => $this->faker->boolean(70)],
        ];
    }

    private function generatePartsUsed(): array
    {
        $parts = [
            ['name' => 'Bearing 6304', 'quantity' => 2, 'part_number' => 'BRG-6304'],
            ['name' => 'Oil Seal', 'quantity' => 1, 'part_number' => 'SL-45-60'],
            ['name' => 'Grease', 'quantity' => 1, 'part_number' => 'GRS-LG2'],
            ['name' => 'Contactor', 'quantity' => 1, 'part_number' => 'CTC-9A'],
        ];

        return $this->faker->randomElements($parts, rand(0, 3));
    }

    // ---------- State methods ----------

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function verified(): static
    {
        return $this->state(fn () => ['status' => 'verified']);
    }

    public function emergency(): static
    {
        return $this->state(fn () => [
            'type' => 'emergency',
            'scheduled_date' => now()->addHours(2),
            'status' => 'pending',
        ]);
    }

    public function preventive(): static
    {
        return $this->state(fn () => [
            'type' => 'preventive',
            'scheduled_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'status' => 'pending',
        ]);
    }
}