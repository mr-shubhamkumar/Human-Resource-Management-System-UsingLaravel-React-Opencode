<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Seed company announcements.
     */
    public function run(): void
    {
        $author = User::where('email', 'admin@company.com')->first();

        if ($author === null) {
            return;
        }

        $announcements = [
            ['New HR policies effective next month', 'Please review the updated employee handbook in the shared drive.'],
            ['Payroll cut-off reminder', 'Timesheets must be approved by the 25th of each month.'],
            ['Annual performance reviews', 'Performance review forms are now open for all departments.'],
            ['Office maintenance schedule', 'The IT team will perform network maintenance this weekend.'],
            ['Wellness program registration', 'Register for the company wellness program before the end of the month.'],
            ['Holiday schedule', 'The office will be closed for the upcoming public holiday.'],
        ];

        foreach ($announcements as [$title, $content]) {
            Announcement::factory()->create([
                'user_id' => $author->id,
                'created_by' => $author->id,
                'title' => $title,
                'content' => $content,
            ]);
        }
    }
}
