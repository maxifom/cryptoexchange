<?php

namespace App\Http\Controllers;

use App\Jobs\EmailConfirmationJob;
use App\User;
use Illuminate\Http\Request;

class ResendController extends Controller
{
    public function resendEmailConfirmation(Request $request)
    {
        $validated=$request->validate([
            'email'=>'required|exists:users,email',
            'captcha'=>'required|captcha'
        ]);
        $user = User::where('email',$validated['email'])->first();
        EmailConfirmationJob::dispatch($user,true);
    }
}
