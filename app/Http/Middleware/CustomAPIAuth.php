<?php

namespace App\Http\Middleware;

use App\ApiIp;
use Closure;
use App\ApiEntry;
use Illuminate\Http\Response;
use App\User;

class CustomAPIAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app('debugbar')->disable();
        if ($request->isMethod("POST") && $request->input('token') != NULL) {
            $ip = $request->ip();
            $token = $request->post('token');
            $api_entry = ApiEntry::where('token', $token)->first();
            if ($api_entry == null) {
                return $this->ErrorResponse();
            }
            $api_ips = ApiIp::where('api_id', $api_entry->id)->get();
            if ($api_ips == null) {
                return $this->ErrorResponse();
            }
            foreach ($api_ips as $_ip) {
                if ($_ip['api_ip'] == ip2long($ip)) {
                    return $next($request);
                }
            }
        }
        return $this->ErrorResponse();
    }

    protected function ErrorResponse()
    {
        return \response(json_encode(['error' => 'Unauthorized.']), 401);
    }
}
