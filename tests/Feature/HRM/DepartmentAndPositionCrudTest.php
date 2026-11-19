<?php

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('departments support search, approval and CRUD', function () {
    Department::factory()->count(3)->create();
    Department::factory()->create(['name' => 'Unique Team']);

    $this->get(route('departments.index', ['search' => 'Unique']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('departments/index')
            ->has('departments.data', 1)
            ->where('departments.data.0.name', 'Unique Team'));

    $this->post(route('departments.store'), [
        'name' => 'New Team',
        'description' => 'A newly created team.',
        'is_approved' => '1',
    ])->assertRedirect(route('departments.index'));

    $department = Department::where('name', 'New Team')->firstOrFail();
    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'is_approved' => true,
    ]);

    $this->post(route('departments.store'), [
        'name' => 'Pending Team',
        'is_approved' => '0',
    ])->assertRedirect(route('departments.index'));

    $this->assertDatabaseHas('departments', [
        'name' => 'Pending Team',
        'is_approved' => false,
    ]);

    $this->post(route('departments.store'), [
        'name' => 'New Team',
    ])->assertSessionHasErrors('name');

    $this->get(route('departments.edit', $department))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('departments/form'));

    $this->put(route('departments.update', $department), [
        'name' => 'Renamed Team',
        'description' => 'Updated description.',
        'is_approved' => '1',
    ])->assertRedirect(route('departments.index'));

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Renamed Team',
    ]);

    $this->delete(route('departments.destroy', $department))
        ->assertRedirect(route('departments.index'));

    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
});

test('positions support search, salary validation and CRUD', function () {
    $department = Department::factory()->create();
    foreach (['Analyst', 'Manager', 'Coordinator'] as $title) {
        Position::factory()->create([
            'department_id' => $department->id,
            'title' => $title,
        ]);
    }
    Position::factory()->create([
        'department_id' => $department->id,
        'title' => 'Unique Position',
    ]);

    $this->get(route('positions.index', ['search' => 'Unique Position']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('positions/index')
            ->has('positions.data', 1)
            ->where('positions.data.0.title', 'Unique Position'));

    $this->post(route('positions.store'), [
        'department_id' => $department->id,
        'title' => 'New Position',
        'description' => 'Handles new work.',
        'salary_min' => 30000,
        'salary_max' => 60000,
    ])->assertRedirect(route('positions.index'));

    $position = Position::where('title', 'New Position')->firstOrFail();

    $this->post(route('positions.store'), [
        'department_id' => $department->id,
        'title' => 'New Position',
        'salary_min' => 30000,
        'salary_max' => 60000,
    ])->assertSessionHasErrors('title');

    $this->post(route('positions.store'), [
        'department_id' => $department->id,
        'title' => 'Invalid Salary',
        'salary_min' => 60000,
        'salary_max' => 30000,
    ])->assertSessionHasErrors('salary_max');

    $this->get(route('positions.edit', $position))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('positions/form')
            ->where('position.id', $position->id)
            ->has('departments'));

    $this->put(route('positions.update', $position), [
        'department_id' => $department->id,
        'title' => 'Renamed Position',
        'salary_min' => 35000,
        'salary_max' => 70000,
    ])->assertRedirect(route('positions.index'));

    $this->assertDatabaseHas('positions', [
        'id' => $position->id,
        'title' => 'Renamed Position',
    ]);

    $this->delete(route('positions.destroy', $position))
        ->assertRedirect(route('positions.index'));

    $this->assertDatabaseMissing('positions', ['id' => $position->id]);
});
