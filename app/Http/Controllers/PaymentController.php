<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function manualPay(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:expenses,id',
        ]);

        $expenses = Expense::whereIn('id', $request->ids)
            ->where('status', 'approved')
            ->get();

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

        return response()->json(['message' => 'Payments processed']);
    }
}
