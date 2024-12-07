<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class AutoPayment extends Command
{
    protected $signature = 'payment:auto';
    protected $description = 'Automatically process payments for approved expenses.';

    public function handle()
    {
        $expenses = Expense::where('status', 'approved')->get();

        foreach ($expenses as $expense) {
            try {
                $bankCode = substr($expense->iban, 0, 2);

                if (!in_array($bankCode, ['11', '22', '33'])) {
                    throw new \Exception("Unsupported bank for IBAN: {$expense->iban}");
                }

                Payment::create([
                    'expense_id' => $expense->id,
                    'status' => 'completed',
                ]);

                $expense->update(['status' => 'completed']);
                Log::info("Payment completed for expense ID {$expense->id}");
            } catch (\Exception $e) {
                Log::error("Payment failed for expense ID {$expense->id}: " . $e->getMessage());
                Payment::create([
                    'expense_id' => $expense->id,
                    'status' => 'failed',
                ]);
            }
        }

        $this->info('Auto payment process completed.');
    }
}
