<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;


class ApiValidateVixipay
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
        if (!$request->header("Authorization")) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (!$this->validateAll($request)) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }

    public function validateAll($request)
    {
        $bearer = $request->header('Authorization');
        $tokenHash = null;
        $access_token = str_replace('Bearer ', '', $bearer);
        $tokenParts = explode(".", $access_token);
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        $expireIn = Carbon::createFromFormat('Y-m-d H:i:s', $jwtPayload->expire_in)->format('U');
        $now = Carbon::now()->format('U');

        if ($expireIn < $now) {
            return false;
        }
        $expireInMinutes = ($expireIn - $now) / 60;
        if($jwtPayload->user->rol->name === 'Client') {
            $client = Client::where('user_id', $jwtPayload->user->id)->first();

            if (!$client) {
                Client::create([
                    'zip_code' => 53000,
                    'user_id' => $jwtPayload->user->id,
                    'user_json' => json_encode($jwtPayload->user)
                ]);
            } else {
                $client->user_json = json_encode($jwtPayload->user);
                $client->save();
            }
        }

        Session::put('user', $jwtPayload->user, $expireInMinutes);

        return true;
    }


}
