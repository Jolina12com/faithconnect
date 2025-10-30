<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BulkEmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);
        
        // Parse emails (comma or newline separated)
        $emails = preg_split('/[,\n\r]+/', $request->emails);
        $emails = array_filter(array_map('trim', $emails), function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        
        if (empty($emails)) {
            return response()->json(['success' => false, 'message' => 'No valid emails found']);
        }
        
        try {
            Mail::raw($request->message, function($mail) use ($emails, $request) {
                $mail->to($emails)->subject($request->subject);
            });
            
            return response()->json([
                'success' => true, 
                'message' => "Email sent to " . count($emails) . " recipients"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}