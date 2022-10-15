<?php

namespace Eighteen73\Turnstile\Http\Middleware;

use Closure;
use Eighteen73\Turnstile\Rules\Turnstile as TurnstileRule;
use Illuminate\Support\Facades\Validator;

class Turnstile
{
    const INPUT_NAME = 'cf-turnstile-response';

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, Closure $next)
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
