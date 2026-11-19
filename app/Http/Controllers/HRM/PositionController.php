<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\PositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    /**
     * Display a paginated, searchable list of positions.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $positions = Position::query()
            ->with('department:id,name')
            ->withCount('employees')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('department', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('positions/index', [
            'positions' => $positions,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new position.
     */
    public function create(): Response
    {
        return Inertia::render('positions/form', [
            'position' => null,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created position.
     */
    public function store(PositionRequest $request): RedirectResponse
    {
        Position::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Position created successfully.']);

        return to_route('positions.index');
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position): Response
    {
        return Inertia::render('positions/form', [
            'position' => $position,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified position.
     */
    public function update(PositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Position updated successfully.']);

        return to_route('positions.index');
    }

    /**
     * Remove the specified position.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Position deleted successfully.']);

        return to_route('positions.index');
    }
}
