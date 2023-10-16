<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Support\Facades\Auth;


class LogMiddleware
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
        $response = $next($request);

        if ($response->status() == 500 || $response->status() == 404) {
            $content = $response->content();

            $error = json_decode($content);
            if($error) {
                $guard = Auth::guard();
                $user_id = null;
                if ($guard) {
                    $user = Auth::user();
                    $user_id = $user ? $user->id : null;
                }

                $log = Log::create([
                    'description' => isset($error) && $error->message ? $error->message : '',
                    'error' => isset($error->error) ? $error->error : null,
                    'petition' => $request->method() . "//" . $request->url(),
                    'parameters' => json_encode($request->all()),
                    'user_id' => $user_id,
                ]);

                $error->message = isset($error) && isset($error->message) ? $error->message . " " . trans('msgs.msg_reference_number', ['var' => $log->id]) : '';
                if (isset($error->error)) {
                    unset($error->error);
                }
                $response->setContent(json_encode($error));
            }

        }

        return $response;

    }


}
