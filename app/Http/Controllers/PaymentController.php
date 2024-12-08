<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function processPayment(ExpenseRequest $request)
    {
        $iban = $request->user->iban;
        $amount = $request->amount;

        // شناسایی بانک
        $bankCode = substr($iban, 0, 2);
        $bankName = $this->getBankNameByCode($bankCode);

        if (!$bankName) {
            return ['success' => false, 'message' => 'Bank not supported.'];
        }

        try {
        
            // callBankApi($bankName, $iban, $amount);
            $request->status = 'paid';
            $request->save();

            Log::create([
                'type' => 'payment',
                'details' => "Payment of {$amount} to {$iban} via {$bankName} successful.",
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::create([
                'type' => 'error',
                'details' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Payment failed.'];
        }
    }

    private function getBankNameByCode($code)
    {
        $banks = [
            '11' => 'meli',
            '22' => 'pasargad',
            '33' => 'melat',
        ];

        return $banks[$code] ?? null;
    }
    
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
                
                if (is_null($this->getBankNameByCode($bankCode))) {
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
