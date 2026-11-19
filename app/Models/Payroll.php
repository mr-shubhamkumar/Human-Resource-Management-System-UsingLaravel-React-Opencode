<?php

namespace App\Models;

use Database\Factories\PayrollFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $employee_id
 * @property string $basic_salary
 * @property string $bonus
 * @property string $deductions
 * @property string $net_salary
 * @property Carbon $pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee $employee
 * @property-read Collection<int, Payslip> $payslips
 */
#[Fillable([
    'employee_id',
    'basic_salary',
    'bonus',
    'deductions',
    'net_salary',
    'pay_date',
])]
class Payroll extends Model
{
    /** @use HasFactory<PayrollFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'pay_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return HasMany<Payslip, $this>
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
