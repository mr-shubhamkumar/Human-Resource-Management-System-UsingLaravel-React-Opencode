<?php

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard reports live workforce statistics and recent projects', function () {
    $this->actingAs(User::factory()->create());

    $department = Department::factory()->create();
    $position = Position::factory()->create(['department_id' => $department->id]);
    $employees = Employee::factory()->count(3)->create([
        'department_id' => $department->id,
        'position_id' => $position->id,
    ]);
    Employee::factory()->pending()->create([
        'department_id' => $department->id,
        'position_id' => $position->id,
    ]);

    $leaveType = LeaveType::factory()->create();
    Leave::factory()->create([
        'employee_id' => $employees[0]->id,
        'leave_type_id' => $leaveType->id,
        'status' => 'approved',
        'start_date' => today()->subDay(),
        'end_date' => today()->addDay(),
    ]);
    Leave::factory()->create([
        'employee_id' => $employees[1]->id,
        'leave_type_id' => $leaveType->id,
        'status' => 'pending',
    ]);
    Leave::factory()->create([
        'employee_id' => $employees[2]->id,
        'leave_type_id' => $leaveType->id,
        'status' => 'approved',
        'start_date' => today()->subDays(10),
        'end_date' => today()->subDays(8),
    ]);

    Attendance::factory()->create([
        'employee_id' => $employees[0]->id,
        'date' => today(),
        'status' => 'present',
    ]);
    Payroll::factory()->create([
        'employee_id' => $employees[0]->id,
        'pay_date' => today()->endOfMonth(),
        'net_salary' => 50000,
    ]);
    $project = Project::factory()->create([
        'title' => 'Employee Self-Service Portal',
        'progress' => 85,
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('stats.total_employees', 4)
            ->where('stats.employees_on_leave', 1)
            ->where('stats.pending_employees', 1)
            ->where('stats.pending_leaves', 1)
            ->where('stats.department_count', 1)
            ->where('charts.employees_by_department.labels.0', $department->name)
            ->where('charts.employees_by_department.data.0', 4)
            ->where('charts.attendance.labels', fn ($labels) => count($labels) === 30)
            ->where('charts.leave_status.data', [1, 2, 0])
            ->where('recent_projects.0.title', $project->title)
            ->where('recent_projects.0.progress', 85)
            ->has('charts.payroll_trend.data')
        );
});
