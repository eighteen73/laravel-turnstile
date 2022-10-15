<?php

namespace Eighteen73\Turnstile\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $error = $this->checkResponseCode($value);

        if ($error) {
            $fail($error);
        }
    }

    /**
     * Check the supplied response code against the Turnstile API. Return an error message if there is one, or else
     * null.
     *
     * @param $response_code
     * @return string|null
     */
    protected function checkResponseCode($response_code)
    {
        $data = [
            'secret' => config('turnstile.secret'),
            'response' => $response_code,
        ];
        if (config('turnstile.send_client_ip')) {
            $data['remoteip'] = request()->getClientIp();
        }
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $data);

        // All good, no error to return
        if ($response->json('success')) {
            return null;
        }

        // If there was an error we'll just display the first one
        // Ref. https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
        $errors = $response->json('error-codes');
        switch (array_shift($errors)) {
            case 'missing-input-secret':
                $this->logError('The secret parameter was not passed.');
                break;
            case 'invalid-input-secret':
                $this->logError('The secret parameter was invalid or did not exist.');
                break;
            case 'missing-input-response':
                $this->logError('The response parameter was not passed.');
                break;
            case 'invalid-input-response':
                $this->logError('The response parameter is invalid or has expired.');
                break;
            case 'bad-request':
                $this->logError('The request was rejected because it was malformed.');
                break;
            case 'timeout-or-duplicate':
                $this->logError('The response parameter has already been validated before.');
                break;
            case 'internal-error':
                $this->logError('An internal error happened while validating the response. The request can be retried.');
                break;
        }

        return 'The website could not confirm that you are human. Please try submitting the form again.';
    }

    protected function logError($error)
    {
        if (! config('turnstile.log_errors')) {
            return;
        }

        Log::debug("Turnstile: $error");
    }
}
