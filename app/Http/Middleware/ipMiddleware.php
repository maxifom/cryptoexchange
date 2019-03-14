<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
class ipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->post('email')==NULL)
        {
            return $next($request);
        }
        if (Auth::user()!=NULL)
        {
            $user = Auth::user();
        }
        else
        {
            $user = User::where('email',$request->post('email'))->first();
        }
        if ($user===null)
        {
            return redirect()->back()->with(['status'=>'Invalid email or password']);
        }
        $user_ips = $user->ips;
        $ips = $user_ips->pluck('ip')->toArray();
        for ($i=0;$i<count($ips);$i++)
        {
            $ips[$i]=long2ip($ips[$i]);
        }
        if (in_array($request->ip(),$ips) && $ips!=null)
        {
            return $next($request);
        }
        else
        {
            \App\Jobs\ipConfirmation::dispatch($request->server('REMOTE_ADDR'),$user->id);
            return redirect('confirmIp')->with(['status'=>'Check your email for new ip confirmation']);
        }
    }
}
