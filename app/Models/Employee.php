<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $employee_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $gender
 * @property Carbon|null $date_of_birth
 * @property string|null $address
 * @property Carbon $hire_date
 * @property int $department_id
 * @property int $position_id
 * @property string $status
 * @property bool $is_approved
 * @property string|null $profile_photo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $full_name
 * @property-read string|null $profile_photo_url
 * @property-read Department $department
 * @property-read Position $position
 * @property-read Collection<int, Attendance> $attendances
 * @property-read Collection<int, Leave> $leaves
 * @property-read Collection<int, Payroll> $payrolls
 */
#[Fillable([
    'employee_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'gender',
    'date_of_birth',
    'address',
    'hire_date',
    'department_id',
    'position_id',
    'status',
    'is_approved',
    'profile_photo',
])]
class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $appends = ['full_name', 'profile_photo_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => $this->first_name.' '.$this->last_name);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->profile_photo
                ? Storage::disk('public')->url($this->profile_photo)
                : null,
        );
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<Leave, $this>
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * @return HasMany<Payroll, $this>
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
