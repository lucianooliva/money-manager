<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? response()->json(["emailVerified" => TRUE])
                    : response()->json(["emailVerified" => FALSE]);
    }

    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasVerifiedEmail()) {
            return view('email-verified-successfully');
        }
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return view('email-verified-successfully');
    }
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["emailVerified" => TRUE]);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json(["emailVerified" => FALSE, "resent" => TRUE]);
    }
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["emailVerified" => TRUE]);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json(["emailVerified" => FALSE, "resent" => TRUE]);
    }
}
