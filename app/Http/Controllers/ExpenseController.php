<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'iban' => 'required|string|size:26',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $user = User::where('national_code', $request->user()->national_code)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found in the database'], 404);
        }

        $attachmentPath = $request->file('attachment')
            ? $request->file('attachment')->store('attachments', 'public')
            : null;

        $expense = Expense::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'iban' => $request->iban,
            'attachment_path' => $attachmentPath,
        ]);

        return response()->json(['expense' => $expense, 'message' => 'Expense created successfully'], 201);
    }

    public function index(Request $request)
    {
        $expenses = Expense::with(['user', 'category'])
            ->when($request->user()->role === 'user', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->get();

        return response()->json(['expenses' => $expenses]);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:expenses,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        $expenses = Expense::whereIn('id', $request->ids)->get();

        foreach ($expenses as $expense) {
            $expense->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            ]);
        }

        return response()->json(['message' => 'Expenses updated successfully']);
    }
}
