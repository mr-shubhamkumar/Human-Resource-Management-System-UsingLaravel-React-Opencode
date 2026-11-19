<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\Payslip;
use Illuminate\Database\Seeder;

class PayslipSeeder extends Seeder
{
    /**
     * Seed one payslip for every payroll record.
     */
    public function run(): void
    {
        $payrolls = Payroll::with('employee')->get();
        $rows = [];

        foreach ($payrolls as $payroll) {
            $generatedAt = $payroll->pay_date->copy()->subDay()->setTime(10, 30);
            $attributes = Payslip::factory()->make([
                'payroll_id' => $payroll->id,
                'generated_at' => $generatedAt->toDateTimeString(),
                'file_path' => sprintf(
                    'payslips/%s/%s/payslip-%d.pdf',
                    $payroll->employee->employee_id,
                    $payroll->pay_date->format('Y-m'),
                    $payroll->id,
                ),
            ])->getAttributes();

            $attributes['created_at'] = $generatedAt->toDateTimeString();
            $attributes['updated_at'] = $generatedAt->toDateTimeString();
            $rows[] = $attributes;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Payslip::query()->insert($chunk);
        }
    }
}
