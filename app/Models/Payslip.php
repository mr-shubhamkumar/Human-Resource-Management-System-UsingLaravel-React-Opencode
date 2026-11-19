<?php

namespace App\Models;

use Database\Factories\PayslipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $payroll_id
 * @property Carbon $generated_at
 * @property string $file_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Payroll $payroll
 */
#[Fillable(['payroll_id', 'generated_at', 'file_path'])]
class Payslip extends Model
{
    /** @use HasFactory<PayslipFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Payroll, $this>
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
