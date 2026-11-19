<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seed projects used by the dashboard overview.
     */
    public function run(): void
    {
        $owner = User::where('email', 'admin@company.com')->first();

        if ($owner === null) {
            return;
        }

        $projects = [
            ['Employee Self-Service Portal', 'Internal', 85, 'in_progress'],
            ['Payroll Automation', 'Finance Team', 60, 'in_progress'],
            ['Recruitment Pipeline Redesign', 'Talent Acquisition', 100, 'completed'],
            ['HR Analytics Dashboard', 'Executive', 45, 'in_progress'],
            ['Onboarding Experience Upgrade', 'People Ops', 30, 'in_progress'],
            ['Performance Review System', 'Internal', 100, 'completed'],
            ['Attendance Mobile App', 'Operations', 20, 'in_progress'],
            ['Benefits Administration Module', 'Internal', 10, 'planning'],
            ['Employee Engagement Survey', 'People Ops', 75, 'in_progress'],
            ['Compliance Documentation Audit', 'Legal Team', 55, 'in_progress'],
        ];

        foreach ($projects as [$title, $client, $progress, $status]) {
            $startDate = today()->subDays(fake()->numberBetween(30, 180));

            Project::factory()->create([
                'user_id' => $owner->id,
                'title' => $title,
                'client' => $client,
                'details' => sprintf('%s initiative managed by the HR department.', $title),
                'project_start_date' => $startDate->toDateString(),
                'project_end_date' => $startDate->copy()->addMonths(fake()->numberBetween(2, 6))->toDateString(),
                'progress' => $progress,
                'status' => $status,
            ]);
        }
    }
}
