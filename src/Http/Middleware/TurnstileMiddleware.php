<?php

namespace Eighteen73\Turnstile\Http\Middleware;

use Closure;
use Eighteen73\Turnstile\Rules\TurnstileRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TurnstileMiddleware
{
    const INPUT_NAME = 'cf-turnstile-response';

    public function handle(Request $request, Closure $next)
    {
        // We're only interested in requests with a Turnstile response code
        if (! $request->has(self::INPUT_NAME)) {
            return $next($request);
        }

        $response_code = $request->get(self::INPUT_NAME);

        $validator = Validator::make([
            self::INPUT_NAME => $response_code,
        ], [
            self::INPUT_NAME => [new TurnstileRule],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        return $next($request);
    }
}
