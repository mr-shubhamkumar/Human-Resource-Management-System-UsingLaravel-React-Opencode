<?php

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->employee = Employee::factory()->create();
});

test('attendance records can be created, edited and deleted', function () {
    foreach (range(1, 16) as $daysAgo) {
        Attendance::factory()->create([
            'employee_id' => $this->employee->id,
            'date' => today()->subDays($daysAgo)->toDateString(),
        ]);
    }

    $this->get(route('attendances.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('attendances/index')
            ->has('attendances.data', 15)
            ->where('attendances.total', 16));

    $this->get(route('attendances.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('attendances/form')
            ->has('employees'));

    $this->post(route('attendances.store'), [
        'employee_id' => $this->employee->id,
        'date' => today()->toDateString(),
        'check_in' => '08:15',
        'check_out' => '17:30',
        'status' => 'late',
    ])->assertRedirect(route('attendances.index'));

    $attendance = Attendance::whereDate('date', today())->firstOrFail();

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance->id,
        'status' => 'late',
    ]);

    $this->post(route('attendances.store'), [
        'employee_id' => $this->employee->id,
        'date' => today()->toDateString(),
        'status' => 'present',
    ])->assertSessionHasErrors('date');

    $this->put(route('attendances.update', $attendance), [
        'employee_id' => $this->employee->id,
        'date' => $attendance->date->toDateString(),
        'check_in' => '08:00',
        'check_out' => '17:00',
        'status' => 'present',
    ])->assertRedirect(route('attendances.index'));

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance->id,
        'status' => 'present',
    ]);

    $this->delete(route('attendances.destroy', $attendance))
        ->assertRedirect(route('attendances.index'));

    $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
});

test('leave requests can be created with an approver and deleted', function () {
    $leaveType = LeaveType::factory()->create();

    $this->get(route('leaves.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('leaves/form')
            ->has('leaveTypes')
            ->has('approvers'));

    $this->post(route('leaves.store'), [
        'employee_id' => $this->employee->id,
        'leave_type_id' => $leaveType->id,
        'start_date' => today()->toDateString(),
        'end_date' => today()->addDays(2)->toDateString(),
        'reason' => 'Medical consultation.',
        'status' => 'approved',
    ])->assertRedirect(route('leaves.index'));

    $leave = Leave::firstOrFail();
    $this->assertDatabaseHas('leaves', [
        'id' => $leave->id,
        'status' => 'approved',
        'approved_by' => auth()->id(),
    ]);

    $this->post(route('leaves.store'), [
        'employee_id' => $this->employee->id,
        'leave_type_id' => $leaveType->id,
        'start_date' => today()->toDateString(),
        'end_date' => today()->subDay()->toDateString(),
        'reason' => 'Invalid dates.',
        'status' => 'pending',
    ])->assertSessionHasErrors('end_date');

    $this->put(route('leaves.update', $leave), [
        'employee_id' => $this->employee->id,
        'leave_type_id' => $leaveType->id,
        'start_date' => today()->toDateString(),
        'end_date' => today()->toDateString(),
        'reason' => 'Family matter.',
        'status' => 'rejected',
    ])->assertRedirect(route('leaves.index'));

    $this->assertDatabaseHas('leaves', [
        'id' => $leave->id,
        'status' => 'rejected',
        'reason' => 'Family matter.',
    ]);

    $this->delete(route('leaves.destroy', $leave))
        ->assertRedirect(route('leaves.index'));

    $this->assertDatabaseMissing('leaves', ['id' => $leave->id]);
});

test('payroll net salary is calculated and payslips are managed', function () {
    $this->post(route('payrolls.store'), [
        'employee_id' => $this->employee->id,
        'basic_salary' => 50000,
        'bonus' => 5000,
        'deductions' => 7500,
        'pay_date' => today()->endOfMonth()->toDateString(),
    ])->assertRedirect(route('payrolls.index'));

    $payroll = Payroll::firstOrFail();
    $this->assertDatabaseHas('payrolls', [
        'id' => $payroll->id,
        'net_salary' => 47500,
    ]);

    $this->post(route('payrolls.store'), [
        'employee_id' => $this->employee->id,
        'basic_salary' => 50000,
        'pay_date' => $payroll->pay_date->toDateString(),
    ])->assertSessionHasErrors('pay_date');

    $this->post(route('payslips.store'), [
        'payroll_id' => $payroll->id,
        'generated_at' => now()->toDateTimeString(),
        'file_path' => 'payslips/EMP0001/payslip.pdf',
    ])->assertRedirect(route('payslips.index'));

    $payslip = Payslip::firstOrFail();

    $this->get(route('payslips.index', ['search' => 'payslip.pdf']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('payslips/index')
            ->has('payslips.data', 1));

    $this->put(route('payslips.update', $payslip), [
        'payroll_id' => $payroll->id,
        'file_path' => 'payslips/EMP0001/updated.pdf',
    ])->assertRedirect(route('payslips.index'));

    $this->assertDatabaseHas('payslips', [
        'id' => $payslip->id,
        'file_path' => 'payslips/EMP0001/updated.pdf',
    ]);

    $this->delete(route('payslips.destroy', $payslip))
        ->assertRedirect(route('payslips.index'));

    $this->assertDatabaseMissing('payslips', ['id' => $payslip->id]);
});
