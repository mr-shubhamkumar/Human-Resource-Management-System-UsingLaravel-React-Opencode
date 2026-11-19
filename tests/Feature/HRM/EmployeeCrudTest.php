<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->department = Department::factory()->create();
    $this->position = Position::factory()->create([
        'department_id' => $this->department->id,
    ]);
});

function employeePayload(array $overrides = []): array
{
    return array_merge([
        'employee_id' => 'EMP9001',
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan.delacruz@company.com',
        'hire_date' => now()->toDateString(),
        'department_id' => Department::first()->id,
        'position_id' => Position::first()->id,
        'status' => 'active',
        'is_approved' => '1',
    ], $overrides);
}

test('employees can be listed, paginated and searched', function () {
    Employee::factory()->count(18)->create([
        'department_id' => $this->department->id,
        'position_id' => $this->position->id,
    ]);
    Employee::factory()->create([
        'employee_id' => 'EMP7777',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'department_id' => $this->department->id,
        'position_id' => $this->position->id,
    ]);

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employees/index')
            ->has('employees.data', 15)
            ->where('employees.total', 19)
            ->where('employees.per_page', 15));

    $this->get(route('employees.index', ['search' => 'EMP7777']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('employees.data', 1)
            ->where('employees.data.0.first_name', 'Maria')
            ->where('filters.search', 'EMP7777'));
});

test('an employee can be created with the auto-approval option', function () {
    $this->post(route('employees.store'), employeePayload())
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP9001',
        'email' => 'juan.delacruz@company.com',
        'is_approved' => true,
    ]);

    $this->post(route('employees.store'), employeePayload([
        'employee_id' => 'EMP9002',
        'email' => 'pending@company.com',
        'is_approved' => '0',
    ]))->assertRedirect(route('employees.index'));

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP9002',
        'is_approved' => false,
    ]);
});

test('employee validation rejects duplicate employee ids and emails', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP9001',
        'email' => 'existing@company.com',
        'department_id' => $this->department->id,
        'position_id' => $this->position->id,
    ]);

    $this->post(route('employees.store'), employeePayload([
        'email' => 'new@company.com',
    ]))->assertSessionHasErrors('employee_id');

    $this->post(route('employees.store'), employeePayload([
        'employee_id' => 'EMP9002',
        'email' => 'existing@company.com',
    ]))->assertSessionHasErrors('email');
});

test('an employee can be edited, updated and deleted', function () {
    $employee = Employee::factory()->create([
        'department_id' => $this->department->id,
        'position_id' => $this->position->id,
    ]);

    $this->get(route('employees.edit', $employee))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employees/form')
            ->where('employee.id', $employee->id)
            ->has('departments')
            ->has('positions'));

    $this->put(route('employees.update', $employee), employeePayload([
        'employee_id' => $employee->employee_id,
        'email' => $employee->email,
        'first_name' => 'Maria',
        'status' => 'inactive',
        'is_approved' => '1',
    ]))->assertRedirect(route('employees.index'));

    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'first_name' => 'Maria',
        'status' => 'inactive',
    ]);

    $this->delete(route('employees.destroy', $employee))
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
});

test('employee profile photos can be uploaded, replaced and deleted', function () {
    Storage::fake('public');

    $this->post(route('employees.store'), employeePayload([
        'profile_photo' => UploadedFile::fake()->image('juan.jpg', 200, 200),
    ]))->assertRedirect(route('employees.index'));

    $employee = Employee::where('employee_id', 'EMP9001')->firstOrFail();
    expect($employee->profile_photo)->toStartWith('employees/profile-photos/');
    Storage::disk('public')->assertExists($employee->profile_photo);

    $originalPath = $employee->profile_photo;

    $this->put(route('employees.update', $employee), employeePayload([
        'employee_id' => $employee->employee_id,
        'email' => $employee->email,
        'profile_photo' => UploadedFile::fake()->image('juan-new.png', 200, 200),
    ]))->assertRedirect(route('employees.index'));

    $employee->refresh();
    expect($employee->profile_photo)->not->toBe($originalPath);
    Storage::disk('public')->assertMissing($originalPath);
    Storage::disk('public')->assertExists($employee->profile_photo);

    $updatedPath = $employee->profile_photo;

    $this->delete(route('employees.destroy', $employee))
        ->assertRedirect(route('employees.index'));

    Storage::disk('public')->assertMissing($updatedPath);
});

test('employee profile photo validation rejects unsupported files', function () {
    $this->post(route('employees.store'), employeePayload([
        'profile_photo' => UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
    ]))->assertSessionHasErrors('profile_photo');
});
