<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Support\Facades\Mail;

class NotificationController
{
    public static function sendNotification($expense)
    {
        if ($expense->status === 'rejected') {
            // SmsService::send($expense->user->phone, "Your expense request has been rejected.");

            try {
                Mail::raw("Your expense request has been rejected. Reason: {$expense->rejection_reason}", function ($message) use ($expense) {
                    $message->to($expense->user->email)->subject('Expense Request Rejected');
                });
            } catch (\Exception $e) {
                \Log::error("Failed to send email to {$expense->user->email}: " . $e->getMessage());
            }
        }
    }
}
