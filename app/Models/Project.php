<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $client
 * @property string|null $details
 * @property Carbon $project_start_date
 * @property Carbon $project_end_date
 * @property int $progress
 * @property string $status
 * @property Carbon|null $created_at
 * @property-read User $user
 */
#[Fillable([
    'user_id',
    'title',
    'client',
    'details',
    'project_start_date',
    'project_end_date',
    'progress',
    'status',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_start_date' => 'date',
            'project_end_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
