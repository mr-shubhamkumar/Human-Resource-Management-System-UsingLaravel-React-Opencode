<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Project;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the HR dashboard with live statistics and chart data.
     */
    public function index(): Response
    {
        $today = today();
        $startDate = $today->copy()->subDays(29);

        $attendances = Attendance::query()
            ->whereBetween('date', [$startDate, $today])
            ->get(['date', 'status']);
        $attendanceByDate = $attendances->groupBy(fn ($attendance) => $attendance->date->format('Y-m-d'));
        $attendanceLabels = collect(range(0, 29))
            ->map(fn (int $day) => $startDate->copy()->addDays($day)->format('M d'));

        $attendanceSeries = collect(['present', 'late', 'absent'])->mapWithKeys(fn (string $status): array => [
            $status => $attendanceLabels->map(function (string $label, int $index) use ($startDate, $attendanceByDate, $status): int {
                $date = $startDate->copy()->addDays($index)->format('Y-m-d');

                return $attendanceByDate->get($date)?->where('status', $status)->count() ?? 0;
            })->values()->all(),
        ]);

        $leaveCounts = Leave::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $payrollTotals = Payroll::query()
            ->where('pay_date', '>=', $today->copy()->subMonths(6))
            ->orderBy('pay_date')
            ->get(['pay_date', 'net_salary'])
            ->groupBy(fn (Payroll $payroll) => $payroll->pay_date->format('M Y'))
            ->map(fn (Collection $payrolls) => round($payrolls->sum(fn (Payroll $payroll) => (float) $payroll->net_salary), 2));

        $recentProjects = Project::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'user_id', 'title', 'client', 'progress', 'status', 'project_start_date', 'created_at'])
            ->map(fn (Project $project): array => [
                'id' => $project->id,
                'title' => $project->title,
                'client' => $project->client,
                'progress' => $project->progress,
                'status' => $project->status,
                'start_date' => $project->project_start_date->format('M d, Y'),
                'owner' => $project->user?->name,
            ]);

        return Inertia::render('dashboard', [
            'stats' => [
                'total_employees' => Employee::count(),
                'employees_on_leave' => Leave::query()
                    ->where('status', 'approved')
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->distinct()
                    ->count('employee_id'),
                'department_count' => Department::count(),
                'pending_employees' => Employee::query()->where('is_approved', false)->count(),
                'pending_leaves' => Leave::query()->where('status', 'pending')->count(),
            ],
            'charts' => [
                'employees_by_department' => [
                    'labels' => Department::query()->withCount('employees')->orderBy('name')->pluck('name')->all(),
                    'data' => Department::query()->withCount('employees')->orderBy('name')->pluck('employees_count')->all(),
                ],
                'attendance' => [
                    'labels' => $attendanceLabels->all(),
                    'present' => $attendanceSeries['present'],
                    'late' => $attendanceSeries['late'],
                    'absent' => $attendanceSeries['absent'],
                ],
                'leave_status' => [
                    'labels' => ['Pending', 'Approved', 'Rejected'],
                    'data' => [
                        (int) ($leaveCounts['pending'] ?? 0),
                        (int) ($leaveCounts['approved'] ?? 0),
                        (int) ($leaveCounts['rejected'] ?? 0),
                    ],
                ],
                'payroll_trend' => [
                    'labels' => $payrollTotals->keys()->all(),
                    'data' => $payrollTotals->values()->all(),
                ],
            ],
            'recent_projects' => $recentProjects->all(),
        ]);
    }
}
